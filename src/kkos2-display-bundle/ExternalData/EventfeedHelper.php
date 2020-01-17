<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\ExternalData;

use DateTime;
use Kkos2\KkOs2DisplayIntegrationBundle\Slides\DateTrait;
use Psr\Log\LoggerInterface;

/**
 * Class EventfeedHelper
 *
 * @package Kkos2\KkOs2DisplayIntegrationBundle\ExternalData
 */
class EventfeedHelper {

  use DateTrait;

  /**
   * @var \Psr\Log\LoggerInterface $logger
   */
  private $logger;
  /**
   * @var \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\MultisiteCrawler
   */
  private $crawler;

  /**
   * EventfeedHelper constructor.
   *
   * @param \Psr\Log\LoggerInterface $logger
   * @param \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\MultisiteCrawler $crawler
   */
  public function __construct(LoggerInterface $logger, MultisiteCrawler $crawler) {
    $this->logger = $logger;
    $this->crawler = $crawler;
  }

  /**
   * @param string $url Url to feed
   * @param integer $numItems Number of items to return from the feed
   * @param array $queryData
   *
   * @return array|mixed
   */
  public function fetchData($url, $numItems, $queryData) {
    $data = [];
    try {
      $fetched = JsonFetcher::fetch($url, $queryData);
      $data = array_slice($fetched, 0, $numItems);
    } catch (\Exception $e) {
      $this->logger->error('There was a problem fetching data from event feed with this url: ' . $url);
    }
    return $data;
  }

  /**
   * @param mixed $image image data - can be array or string
   *
   * @return string
   */
  public function processImage($image) {
    $image = is_array($image) ? current($image) : $image;
    if (strpos($image, '<img') !== false) {
      $imgUrls = $this->crawler->getImageUrls($image, 'img');
      if (!empty($imgUrls[0])) {
        $image = $imgUrls[0];
      }
    }
    return $image;
  }

  /**
   * Get at formatted date from the date in the feed.
   *
   * @param array $startDate the date from the feed
   *
   * @return string
   */
  public function processDate($startDate) {
    $date = DateTime::createFromFormat('d.m.Y', current($startDate));
    if (!$date) {
      return '';
    }
    return $this->getDayName($date) . ' d. ' . $date->format('j') . '. ' . $this->getMonthName($date);
  }

  /**
   * Check if all expected field keys are in the data.
   *
   * @param array $expectedFields fields we want
   * @param array $data array to check for field keys in
   *
   * @return bool
   */
  public function hasRequiredFields($expectedFields, $data) {
    $missing = array_diff($expectedFields, array_keys($data));
    if (!empty($missing)) {
      $this->logger->error('There were fields missing on event slide:' . implode(', ', $missing));
      return false;
    }
    return true;
  }

}
