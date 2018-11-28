<?php
/**
 * @file
 * Service for Kultunaut-feeds for events.
 */

namespace Kkos2\KkOs2DisplayIntegrationBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Os2Display\CoreBundle\Events\CronEvent;

class Kkos2DisplayService
{
  /**
   * @var \Symfony\Bridge\Monolog\Logger $logger
   */
  private $logger;
  /**
   * @var \Symfony\Component\DependencyInjection\ContainerInterface $container
   */
  private $container;

  public function __construct($container)
  {
    $this->container = $container;
    $this->logger = $this->container->get('logger');
  }

  /**
   * ik.onCron event listener.
   *
   * @param CronEvent $event
   */
  public function onCron(CronEvent $event)
  {
    $this->updateEvents();
  }

  /**
   * Fetch and parse Kultunaut feeds and save on slide external data.
   */
  private function updateEvents()
  {
    $slideRepo = $this->container->get('doctrine')->getRepository('Os2DisplayCoreBundle:Slide');

    /** @var \Os2Display\CoreBundle\Entity\Slide[] $slides */
    $slides = $slideRepo->findBySlideType('kultunaut-event');

    $client = new Client();

    foreach ($slides as $slide) {
      $options = $slide->getOptions();
      if (empty($options['source'])) {
        continue;
      }

      $xml = NULL;
      try {
        $xml = $this->fetchKultunautXml($client, $options['source']);
      }
      catch (\Exception $O_o) {
        $this->logger->error('An error occured trying to get XML: ' . $O_o->getMessage());
        continue;
      }

      $events = [];
      $now = new \DateTime();
      foreach ($xml->item as $item) {
        $startdato = \DateTime::createFromFormat('d.m.Y', $item->startdato->item);
        $slutdato = \DateTime::createFromFormat('d.m.Y', $item->slutdato->item);
        // If the event is old or we have hit the max number of desired events,
        // then skip the item.
        if ((count($events) >= $options['rss_number']) || $startdato < $now || $slutdato < $now) {
          break;
        }
        $tid = (string) $item->tid->item[0];
        $eventItem = [
          'title' => (string) $item->overskrift,
          'description' => (string) $item->kortbeskrivelse,
          'date' => $startdato->format('Y-m-d'),
          'tid' => $tid,
          'dateandtime' => $startdato->format('l \d. j. F') . ' kl. ' . $tid,
        ];
        // Low-tech way to avoid duplicates.
        $events[(string)$item->nid] = $eventItem;
      }

      $externalData = [
        'events' => array_values($events),
      ];
      $slide->setExternalData($externalData);
      // Write to the db.
      $entityManager = $this->container->get('doctrine')->getManager();
      $entityManager->flush();
    }
  }

  /**
   * Get a SimpleXmlElement with data from the feed url.
   *
   * @param Client $client
   *   The client to (re)use.
   * @param string $xml_url
   *   The url to fetch the XML from.
   *
   * @throws \UnexpectedValueException
   *   If the feed is malformed or does not contain events.
   *
   * @return \SimpleXMLElement
   *   The xml object parsed from the feed.
   */
  private function fetchKultunautXml(Client $client, $xml_url) {
    $xml = false;
    try {
      $response = $client->get($xml_url, [
        'headers' => [
          'Accept' => 'application/xml'
        ]
      ]);
      $body = $response->getBody();
      $contents = (string) $body;
      libxml_use_internal_errors(true);
      $xml = simplexml_load_string($contents);
    } catch (TransferException $exception) {
      $this->logger->error('Could not fetch Kultutnaut feed from: ' . $xml_url);
    }

    // If the parsing failed, then try to log the errors and then throw an
    // exception.
    if (false === $xml) {
      $errors = [];
      foreach (libxml_get_errors() as $error) {
        $errors[] = $error->message;
      }
      $this->logger->error("Could not parse feed: \n" . implode("\n", $errors));
      throw new \UnexpectedValueException('An error occured when trying to parse Kultunaut feed: ' . $xml_url);
    }

    // If the feed is empty, that's an error too.
    if ($xml->item->count() < 1) {
      throw new \UnexpectedValueException('It seems that the Kultunaut feed: ' . $xml_url . ' does not contain any events.');
    }

    return $xml;
  }

}
