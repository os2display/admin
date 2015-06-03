<?php
/**
 * @file
 * Contains the koba service.
 */

namespace Indholdskanalen\MainBundle\Services;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class KobaService
 * @package Indholdskanalen\MainBundle\Services
 */
class KobaService {
  private $apiKey;
  private $kobaPath;
  private $container;

  /**
   * Constructor.
   *
   * @param $kobaPath
   * @param $apiKey
   * @param $container
   */
  public function __construct($kobaPath, $apiKey, $container) {
    $this->kobaPath = $kobaPath;
    $this->apiKey = $apiKey;
    $this->container = $container;
  }

  /**
   * Get resources by group id.
   *
   * @param string $group
   *
   * @return array
   */
  public function getResources($group = 'default') {
    $url = $this->kobaPath . '/api/resources/group/' . $group . '?apikey=' . $this->apiKey;

    // Build query.
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close connection.
    curl_close($ch);

    if ($http_status === 200) {
      return json_decode($content);
    }

    throw new HttpException($http_status);
  }

  /**
   * Get Bookings for a resource.
   *
   * @throws HttpException
   *
   * @param $resourceMail
   * @param string $group
   * @param $from
   * @param $to
   *
   * @return array
   *   json array.
   */
  public function getResourceBookings($resourceMail, $group, $from, $to) {
    $url = $this->kobaPath . '/api/resources/' . $resourceMail . '/group/' . $group . '/bookings/from/' . $from . '/to/' . $to . '?apikey=' . $this->apiKey;

    // Build query.
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close connection.
    curl_close($ch);

    if ($http_status === 200) {
      return json_decode($content);
    }

    throw new HttpException($http_status);
  }

  /**
   * Update the calendar events for calendar slides.
   */
  public function updateCalendarSlides() {
    // For each calendar slide
    $slides = $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:Slide')->findBySlideType('calendar');
    $todayStart = time() - 3600;
    // Round down to nearest hour
    $todayStart = $todayStart - ($todayStart % 3600);

    $todayEnd = mktime(23, 59, 59);

    // Get data for interest period
    foreach ($slides as $slide) {
      $bookings = array();

      $options = $slide->getOptions();

      foreach ($options['resources'] as $resource) {
        try{
          $resourceBookings = $this->getResourceBookings($resource['mail'], 'default', $todayStart, $todayEnd);

          if (count($resourceBookings) > 0) {
            $bookings = array_merge($bookings, $resourceBookings);
          }
        }
        catch (HttpException $e) {
          // Ignore exceptions. The show must keep running, even though we have no connection to koba.
        }
      }

      // Sort bookings by start time.
      usort($bookings, function($a, $b) {
        return strcmp($a->start_time, $b->start_time);
      });

      // Save in calendarEvents field
      $slide->setCalendarEvents($bookings);

      $this->container->get('doctrine')->getManager()->flush();
    }
  }
}
