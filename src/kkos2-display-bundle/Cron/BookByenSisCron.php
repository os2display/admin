<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Cron;

use DateTime;
use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\BookbyenApiHelper;
use Kkos2\KkOs2DisplayIntegrationBundle\Slides\DateTrait;
use Psr\Log\LoggerInterface;
use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BookByenSisCron
 *
 * @package Kkos2\KkOs2DisplayIntegrationBundle\Cron
 */
class BookByenSisCron implements EventSubscriberInterface {

  use DateTrait;

  /**
   * @var \Symfony\Bridge\Monolog\Logger $logger
   */
  private $logger;

  /**
   * @var \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\BookbyenApiHelper
   */
  private $apiHelper;

  /**
   * BookByenSisCron constructor.
   *
   * @param \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\BookbyenApiHelper $apiHelper
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(LoggerInterface $logger, BookbyenApiHelper $apiHelper) {
    $this->apiHelper = $apiHelper;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'os2displayslidetools.sis_cron.kk_bookbyen_sis_cron' => [
        ['getSlideData'],
      ],
    ];
  }

  /**
   * Get data for event.
   *
   * @param \Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent $event
   */
  public function getSlideData(SlidesInSlideEvent $event) {
    $slide = $event->getSlidesInSlide();
    // Clear errors before run.
    $slide->setOption('cronfetch_error', '');

    $bookByenOptions = $slide->getOption('bookbyen', []);

    $bookings = [];

    try {
      $data = $this->apiHelper->fetchData($bookByenOptions['api_url']);
      if (!empty($data)) {
        $slide->setOption('place', $this->apiHelper->getPlaceName($data));
        $date = new DateTime();
        $today = $this->getDayName($date) . ', ' . $date->format('j') . '. ' . $this->getMonthName($date);
        $slide->setOption('todaysDate', $today);

        if (!empty(array_filter($bookByenOptions['filtering']))) {
          $filters = $bookByenOptions['filtering'];
          $data = $this->apiHelper->filter($data, $filters);
          $slide->setOption('area', $filters['area'] ?? '');
          $slide->setOption('facility', $filters['facility'] ?? '');
        }

        $fields = array_filter($bookByenOptions['useFields']);

        foreach ($data as $item) {
          $processed = $this->processData($item);
          if (!empty($item)) {
            $bookings[] = array_intersect_key($processed, $fields);
          }
        }

        $bookings = array_slice($bookings, 0, $slide->getOption('sis_total_items', 12));
      }
    } catch (\Exception $e) {
      $slide->setOption('cronfetch_error', $e->getMessage());
    }
    $slide->setSubslides($bookings);
  }

  private function processData($data) {
    $time = $this->apiHelper->processTime($data['start'], $data['end']);
    if (empty($time)) {
      // We can't not have a time.
      throw new \Exception("No start time and end time found in data");
    }
    $booking['time'] = $time;
    $booking['username'] = empty($data['user']['name']) ? '' : $data['user']['name'];
    $booking['facility'] = $data['facility']['name'] ?? '';
    $booking['activity'] = empty($data['activity']['name']) ? '' : $data['activity']['name'];
    $booking['note'] = empty($data['infoscreenNote']) ? '' : $data['infoscreenNote'];
    $booking['team'] = empty($data['team']['name']) ? '' : $data['team']['name'];

    $booking['teamleaders'] = $data['team']['teamleaders'] ?? '';
    if (is_array($booking['teamleaders'])) {
      $arr = array_map(function($item) {
        return $item['name'];
      }, $booking['teamleaders']);
      $booking['teamleaders'] = implode(', ', $arr);
    }

    return array_map('trim', $booking);
  }

}
