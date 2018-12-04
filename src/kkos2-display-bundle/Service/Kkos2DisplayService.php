<?php
/**
 * @file
 * Service for KK Os2Display.
 */

namespace Kkos2\KkOs2DisplayIntegrationBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Kkos2\KkOs2DisplayIntegrationBundle\Crawlers\MellemfeedCrawler;
use Kkos2\KkOs2DisplayIntegrationBundle\Crawlers\ServicespotCrawler;
use Os2Display\CoreBundle\Entity\Slide;
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

  /**
   * Low-tech caching of mellemfeed links.
   * @var array $mellemfeedLinks
   */
  private $mellemfeedLinks = [];

  /**
   * Kkos2DisplayService constructor.
   * @param $container
   */
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
    $slideRepo = $this->container->get('doctrine')->getRepository('Os2DisplayCoreBundle:Slide');

    // Update colorful messages.
    $colorMessageSlides = $slideRepo->findBySlideType('color-messages');
    array_map([$this, 'updateColorMessageSlide'], $colorMessageSlides);

    // Update events.
    $eventSlides = $slideRepo->findBySlideType('kultunaut-event');
    array_map([$this, 'updateKultunautEventsSlide'], $eventSlides);
  }

  /**
   * Update a color message slide with data.
   *
   * @param Slide $slide
   *   Slide to update.
   */
  private function updateColorMessageSlide(Slide $slide)
  {
    $options = $slide->getOptions();
    if (empty($options['source'])) {
      return;
    }

    // Get service spots to scrape from the "temporary" mellemfeed solution.
    $servicespotLinks = $this->getMellemfeedLinksForGroup($options['source'], 'servicespots');

    // Get data for each message the slide will display.
    $messages = array_reduce($servicespotLinks, function ($carry, $link) {
      try {
        $crawler = new ServicespotCrawler($link);
        $crawler->crawl();

        $message = $crawler->getContent();
        if (!empty($message)) {
          $carry[] = $message;
        }
      } catch (\Exception $e) {
        $this->logger->error($e->getMessage());
      }
      return $carry;
    }, []);

    $externalData = [
      'messages' => $messages,
    ];
    $slide->setExternalData($externalData);
    $entityManager = $this->container->get('doctrine')->getManager();
    $entityManager->flush();
  }

  /**
   * Update an event slide.
   *
   * @param Slide $slide
   *   Slide to update.
   * @throws \Exception
   *   If there was a problem updating.
   */
  private function updateKultunautEventsSlide(Slide $slide)
  {
    $options = $slide->getOptions();
    if (empty($options['source'])) {
      return;
    }
    $client = new Client();

    $events = [];
    $now = new \DateTime();

    try {
      $xml = $this->fetchKultunautXml($client, $options['source']);

      foreach ($xml->item as $item) {
        $startdato = \DateTime::createFromFormat('d.m.Y', $item->startdato->item);
        $slutdato = \DateTime::createFromFormat('d.m.Y', $item->slutdato->item);
        // If the event is old or we have hit the max number of desired events,
        // then skip the item.
        if ((count($events) >= $options['rss_number']) || $startdato < $now || $slutdato < $now) {
          break;
        }
        $tid = (string)$item->tid->item[0];
        $eventItem = [
          'title' => (string)$item->overskrift,
          'description' => (string)$item->kortbeskrivelse,
          'date' => $startdato->format('Y-m-d'),
          'tid' => $tid,
          'dateandtime' => $startdato->format('l \d. j. F') . ' kl. ' . $tid,
        ];
        // Low-tech way to avoid duplicates.
        $events[(string)$item->nid] = $eventItem;
      }
    }
    catch (\Exception $O_o) {
      $this->logger->error('An error occured trying to update event slide: ' . $O_o->getMessage());
    }

    try {
      $externalData = [
        'events' => $events,
      ];
      $slide->setExternalData($externalData);
      // Write to the db.
      $entityManager = $this->container->get('doctrine')->getManager();
      $entityManager->flush();
    }
    catch (\Exception $O_o) {
      $this->logger->error('An error occured trying save data on event slide: ' . $O_o->getMessage());
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
  private function fetchKultunautXml(Client $client, $xml_url)
  {
    $xml = false;
    try {
      $response = $client->get($xml_url, [
        'headers' => [
          'Accept' => 'application/xml'
        ]
      ]);
      $body = $response->getBody();
      $contents = (string)$body;
      libxml_use_internal_errors(true);
      $xml = simplexml_load_string($contents);
    } catch (TransferException $exception) {
      $this->logger->error('Could not fetch Kultunaut feed from: ' . $xml_url);
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

  /**
   * Get links for a group from the mellemfeed.
   *
   * @param string $mellemfeedUrl
   *   Link to the KK multisite mellemfeed.
   * @param string $group
   *   The name of the grouping in the mellemfeed to get links from.
   *
   * @return array
   *   Array of links from group in mellemfeed.
   */
  private function getMellemfeedLinksForGroup($mellemfeedUrl, $group)
  {
    if (empty($this->mellemfeedLinks[$mellemfeedUrl])) {
      try {
        $crawler = new MellemfeedCrawler($mellemfeedUrl);
        $crawler->crawl();
        $this->mellemfeedLinks[$mellemfeedUrl] = $crawler->getLinkGroups();
      }
      catch (\Exception $e) {
        $this->logger->error("Could not get mellemfeed links for group: ${group}. Error message: " . $e->getMessage());
        $this->mellemfeedLinks[$mellemfeedUrl] = [];
      }
    }
    return empty($this->mellemfeedLinks[$mellemfeedUrl][$group]) ? [] : $this->mellemfeedLinks[$mellemfeedUrl][$group];
  }

}
