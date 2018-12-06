<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Events;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

/**
 * Class KultunautFeedParser
 * @package Kkos2\KkOs2DisplayIntegrationBundle\Events
 */
class KultunautFeedParser
{
  /**
   * @var string $feedUrl
   */
  private $feedUrl;

  /**
   * @var Client $guzzler
   */
  private $guzzler;

  /**
   * @var SimpleXMLElement $xml
   */
  private $xml;

  /**
   * @var array $events
   */
  private $events = [];

  /**
   * @var bool $sorted
   */
  private $sorted = false;

  /**
   * KultunautFeedParser constructor.
   *
   * @param string $feedUrl
   *   The url of the feed to parse.
   */
  public function __construct($feedUrl)
  {
    $this->feedUrl = $feedUrl;
    $this->guzzler = new Client();
  }

  /**
   * Attempt to parse the XML from the feed.
   *
   * @throws \Exception
   *   If fetching or parsing went wrong.
   */
  public function parse()
  {
    if (empty($this->xml)) {
      $this->fetchXml();
    }

    $today = new \DateTime();

    foreach ($this->xml->item as $item) {
      $startdato = \DateTime::createFromFormat('d.m.Y', $item->startdato->item);
      $slutdato = \DateTime::createFromFormat('d.m.Y', $item->slutdato->item);
      // If the event is old or we have hit the max number of desired events,
      // then skip the item.
      if ($startdato < $today || $slutdato < $today) {
        break;
      }
      $timeStamp = $startdato->getTimestamp();
      $eventItem = [
        'title' => html_entity_decode((string) $item->overskrift),
        'teaserText' => html_entity_decode((string)$item->kortbeskrivelse),
        'originalImage' => (string)$item->billede,
        'image' => '',
        'time' => (string) $item->tid->item[0],
        'date' => ucfirst(strftime('%A d. %e. %B', $timeStamp)),
        'timestamp' => $timeStamp,
      ];
      $urlParts = explode('/files/', $eventItem['originalImage']);
      if (!empty($urlParts[1])) {
        $eventItem['image'] = $urlParts[0] . '/files/styles/flexslider_full/public/' . $urlParts[1];
      }
      $this->events[] = $eventItem;
    }
  }

  /**
   * Get a number of upcoming events.
   *
   * @param integer $num
   *   Number of events you want.
   *
   * @return array
   *   Array with $num events in it.
   */
  public function getUpcoming($num)
  {
    $this->sortEvents();
    return array_slice($this->events, 0, $num);
  }

  /**
   * Sort the events by date.
   */
  private function sortEvents()
  {
    if (!$this->sorted) {
      usort($this->events, function($a, $b) {
        if ($a['timestamp'] === $b['timestamp']) {
          return 0;
        }
        return ($a['timestamp'] < $b['timestamp']) ? -1 : 1;
      });
      $this->sorted = true;
    }
  }

  /**
   * Fetch and load the XML from the feed.
   *
   * @throws \Exception
   *   If anything went wrong fetching the XML.
   */
  private function fetchXml()
  {
    try {
      $response = $this->guzzler->get($this->feedUrl, [
        'headers' => [
          'Accept' => 'application/xml'
        ]
      ]);
      $body = $response->getBody();
      $contents = (string) $body;
      libxml_use_internal_errors(true);
      $this->xml = simplexml_load_string($contents);
    } catch (TransferException $exception) {
      throw new \Exception('Could not fetch Kultunaut feed from: ' . $this->feedUrl);
    }

    // If the parsing failed, then try to log the errors and then throw an
    // exception.
    if (false === $this->xml) {
      $errors = [];
      foreach (libxml_get_errors() as $error) {
        $errors[] = $error->message;
      }
      throw new \UnexpectedValueException('An error occured when trying to parse Kultunaut feed: ' . $this->feedUrl);
    }

    // If the feed is empty, that's an error too.
    if ($this->xml->item->count() < 1) {
      throw new \UnexpectedValueException('It seems that the Kultunaut feed: ' . $this->feedUrl . ' does not contain any events.');
    }
  }

}
