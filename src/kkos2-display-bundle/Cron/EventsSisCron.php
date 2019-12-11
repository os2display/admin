<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Cron;

use Kkos2\KkOs2DisplayIntegrationBundle\Slides\EventFeedData;
use Kkos2\KkOs2DisplayIntegrationBundle\Slides\Mock\MockEventsData;
use Psr\Log\LoggerInterface;
use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventsSisCron implements EventSubscriberInterface
{

  /**
   * @var \Symfony\Bridge\Monolog\Logger $logger
   */
  private $logger;

  public function __construct(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  public static function getSubscribedEvents()
  {
    return [
      'os2displayslidetools.sis_cron.kk_events_sis_cron' => [
        ['getSlideData'],
      ]
    ];
  }

  public function getSlideData(SlidesInSlideEvent $event)
  {
    $slide = $event->getSlidesInSlide();
    $numItems = $slide->getOption('sis_total_items', 12);
    $url = $slide->getOption('datafeed_url', '');
    if ($slide->getOption('datafeed_display')) {
      $url .= '?display=' . $slide->getOption('datafeed_display');
    }

    $fetcher = new EventFeedData($this->logger, $url, $numItems);
    $events = $fetcher->getEvents();

    $filterOnPlace = $slide->getOption('datafeed_filter_place', false);
    if ($filterOnPlace) {
      $events = $this->filterOnPlace($events, $filterOnPlace);
    }

    $slide->setSubslides($events);
  }

  /**
   * This is filtering that should have taken place on the feeds end, but we
   * have to do it here.
   *
   * @param array $events
   *   Events to filter.
   * @param $placeName
   *   The name of the place we want the events for.
   *
   * @return array
   */
  private function filterOnPlace($events, $placeName) {
    $filtered = array_filter($events, function($item) use ($placeName) {
      return !empty($item['place']) && ($item['place'] == $placeName);
    });
    // Return array values to make sure the array is keyed sequentially.
    return array_values($filtered);
  }

  public function getMockSlideData(SlidesInSlideEvent $event)
  {
    $slide = $event->getSlidesInSlide();
    $numItems = $slide->getOption('sis_total_items', 12);
    $mockData = new MockEventsData($numItems);
    $events = $mockData->getEvents();
    $slide->setSubslides($events);
  }

}
