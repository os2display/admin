<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\ExternalData;

use DateTime;
use Kkos2\KkOs2DisplayIntegrationBundle\Slides\DateTrait;
use Psr\Log\LoggerInterface;
use Reload\Os2DisplaySlideTools\Slides\SlidesInSlide;

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
   * @var \Reload\Os2DisplaySlideTools\Slides\SlidesInSlide
   */
  private $slide;

  /**
   * @var string
   */
  private $slideType;

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

  public function setSlide(SlidesInSlide $slide, string $slideType) {
    $this->slide = $slide;
    $this->slideType = $slideType;
  }

  /**
   * @param string $url Url to feed
   * @param integer $numItems Number of items to return from the feed
   * @param array $queryData
   *
   * @return array|mixed
   */
  public function fetchData() {
    $url = $this->slide->getOption('datafeed_url', '');
    if (!$this->validateFeedUrl()) {
      throw new \Exception("$url is not a valid {$this->slideType} url.");
    }
    $query = [];
    $filterDisplay = $this->slide->getOption('datafeed_display', '');
    if (!empty($filterDisplay)) {
      $query = [
        'display' => $filterDisplay,
      ];
    }
    return JsonFetcher::fetch($url, $query);
  }

  public function sliceData($data) {
    return array_slice($data, 0, $this->slide->getOption('sis_total_items', 12));
  }

  private function validateFeedUrl() {
    $endsWith = '';
    switch ($this->slideType) {
      case 'kk-events':
        $endsWith = 'os2display-events';
        break;
      case 'kk-eventplakat':
        $endsWith = 'os2display-posters';
        break;
    }
    if (empty($endsWith)) {
      return FALSE;
    }
    return preg_match("@{$endsWith}[?#]?@", $this->slide->getOption('datafeed_url'));
  }

  /**
   * @param mixed $image image data - can be array or string
   *
   * @return string
   */
  public function processImage($image) {
    $image = is_array($image) ? current($image) : $image;
    if (strpos($image, '<img') !== FALSE) {
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
   * Get missing field keys if any.
   *
   * @param array $expectedFields fields we want
   * @param array $data array to check for field keys in
   *
   * @return string
   */
  public function getMissingFieldKeys($expectedFields, $data) {
    $missing = array_diff($expectedFields, array_keys($data));
    return implode(', ', $missing);
  }

}
