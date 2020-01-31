<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Cron;

use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\JsonFetcher;
use Psr\Log\LoggerInterface;
use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Reload\Os2DisplaySlideTools\Slides\SlidesInSlide;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ColorfulMessageSisCron
 *
 * @package Kkos2\KkOs2DisplayIntegrationBundle\Cron
 */
class ColorfulMessageSisCron implements EventSubscriberInterface {

  /**
   * @var \Psr\Log\LoggerInterface $logger
   */
  private $logger;

  /**
   * ColorfulMessageSisCron constructor.
   *
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  /**
   * @return array
   */
  public static function getSubscribedEvents() {
    return [
      'os2displayslidetools.sis_cron.kk_color_messages_sis_cron' => [
        ['getSlideData'],
      ],
    ];
  }

  /**
   * @param \Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent $event
   */
  public function getSlideData(SlidesInSlideEvent $event) {
    $slide = $event->getSlidesInSlide();

    // Make sure that only one subslide pr. slide is set. The value is
    // for the user, but the colorful slides don't support more than one, so
    // enforce it here.
    $slide->setOption('sis_items_pr_slide', 1);
    // Clear errors before run.
    $slide->setOption('cronfetch_error', '');

    $messages = [];
    try {
      $data = $this->fetchData($slide);
      $messages = array_map([$this, 'processColorfulMessages'], $data);
    } catch (\Exception $e) {
      $slide->setOption('cronfetch_error', $e->getMessage());
    }

    $slide->setSubslides($messages);
  }

  /**
   * @param $data
   *
   * @return array
   */
  private function processColorfulMessages($data) {
    $expected_keys = [
      'title_field',
      'body',
      'field_background_color',
    ];
    // Field names change in the feed often. Try to keep up here.
    $placeKey = 'field_display_institution';
    if (isset($data['field_display_institution_spot'])) {
      $placeKey = 'field_display_institution_spot';
    }
    $expected_keys[] = $placeKey;

    $missing = array_diff($expected_keys, array_keys($data));
    if (!empty($missing)) {
      throw new \Exception('There were fields missing on servicespot slide:' . implode(', ', $missing));
    }

    return [
      'place' => html_entity_decode($data[$placeKey]),
      'title' => html_entity_decode($data['title_field']),
      'body' => html_entity_decode($data['body']),
      'background_color' => trim($data['field_background_color']),
    ];
  }

  private function fetchData(SlidesInSlide $slide ) {
    $url = $slide->getOption('datafeed_url', '');
    if (!preg_match("@servicespot-feed[?#]?@", $url)) {
      throw new \Exception("$url is not a valid servicespot feed url.");
    }
    $query = [];
    $filterDisplay = $slide->getOption('datafeed_display', '');
    if (!empty($filterDisplay)) {
      $query = [
        'display' => $filterDisplay,
      ];
    }
    $json = JsonFetcher::fetch($url, $query);
    return array_slice($json, 0, $slide->getOption('sis_total_items', 12));
  }

}
