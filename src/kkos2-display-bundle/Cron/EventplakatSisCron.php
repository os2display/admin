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

    // Clear errors before run.
    $slide->setOption('cronfetch_error', '');

    $events = [];
    try {
      $this->eventfeedHelper->setSlide($slide, 'kk-eventplakat');
      $data = $this->eventfeedHelper->fetchData();

      $data = $this->eventfeedHelper->sliceData($data);
      $events = array_map([$this, 'processEvents'], $data);

    } catch (\Exception $e) {
      $slide->setOption('cronfetch_error', $e->getMessage());
    }

    $slide->setSubslides($events);
  }

  public function processEvents($data) {
    $expectedFields = [
      'startdate',
      'title',
      'field_teaser',
      'billede',
      'time',
    ];

    $missingFields = $this->eventfeedHelper->getMissingFieldKeys($expectedFields, $data);
    if (!empty($missingFields)) {
      throw new \Exception('There were missing fields in feed: ' . $missingFields);
    }

    $event = [
      'title' => html_entity_decode($data['title']),
      'body' => html_entity_decode($data['field_teaser']),
      'image' => $this->eventfeedHelper->processImage($data['billede']),
      'date' => $this->eventfeedHelper->processDate($data['startdate']),
      'time' => current($data['time']),
    ];

    if (!empty($data['field_os2display_free_text_event'])) {
      $event['free_text'] = $data['field_os2display_free_text_event'];
    }

    return array_map('trim', $event);
  }

}
