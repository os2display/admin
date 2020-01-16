<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Cron;


use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\EventfeedHelper;
use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\JsonFetcher;
use Kkos2\KkOs2DisplayIntegrationBundle\Slides\Mock\MockEventplakatData;
use Kkos2\KkOs2DisplayIntegrationBundle\Slides\PlakatEventFeedData;
use Psr\Log\LoggerInterface;
use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventplakatSisCron implements EventSubscriberInterface {

  /**
   * @var \Psr\Log\LoggerInterface $logger
   */
  private $logger;

  /**
   * @var \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\EventfeedHelper
   */
  private $eventfeedHelper;

  public function __construct(LoggerInterface $logger, EventfeedHelper $eventfeedHelper) {
    $this->logger = $logger;
    $this->eventfeedHelper = $eventfeedHelper;
  }

  public static function getSubscribedEvents() {
    return [
      'os2displayslidetools.sis_cron.kk_eventplakat_sis_cron' => [
        ['getSlideData'],
      ],
    ];
  }

  public function getSlideData(SlidesInSlideEvent $event) {
    $slide = $event->getSlidesInSlide();

    // Make sure that only one subslide pr. slide is set. The value is
    // for the user, but the plakat slides don't support more than one, so
    // enforce it here.
    $slide->setOption('sis_items_pr_slide', 1);
    $numItems = $slide->getOption('sis_total_items', 12);
    $url = $slide->getOption('datafeed_url', '');
    $query = [];
    $filterDisplay = $slide->getOption('datafeed_display', '');
    if (!empty($filterDisplay)) {
      $query = [
        'display' => $filterDisplay,
      ];
    }

    $data = $this->eventfeedHelper->fetchData($url, $numItems, $query);
    $events = array_map([$this, 'processEvents'], $data);

    $slide->setSubslides($events);
  }

  public function processEvents($data) {
    $expectedFields = [
      'startdate',
      'title',
      'field_teaser',
      'image',
      'time',
      'field_os2display_free_text_event',
    ];

    if (!$this->eventfeedHelper->hasRequiredFields($expectedFields, $data)) {
      return [];
    }

    $events = [
      'title' => html_entity_decode($data['title']),
      'body' => html_entity_decode($data['field_teaser']),
      'image' => $this->eventfeedHelper->processImage($data['image']),
      'date' => $this->eventfeedHelper->processDate($data['startdate']),
      'time' => current($data['time']),
      'place' => $data['field_os2display_free_text_event'],
    ];

    return array_map('trim', $events);
  }

}
