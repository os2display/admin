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

  public function __construct($kobaPath, $apiKey, $container) {
    $this->kobaPath = $kobaPath;
    $this->apiKey = $apiKey;
    $this->container = $container;
  }

  public function getResources($group = 'DEFAULT') {
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
    else {
      throw new HttpException($http_status);
    }
  }

  public function getBookingsForResource($resourceMail, $group = 'DEFAULT', $from, $to) {
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
    else {
      throw new HttpException($http_status);
    }
  }

  public function updateCalendarSlides() {
    // For each calendar slide
    $slides = $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:CalendarSlide')->findAll();
    $todayStart = mktime(0, 0, 0, date('n'), date('j'));
    $tomorrowStart = mktime(0, 0, 0, date('n'), date('j') + 1);

    $bookings = array();

    // Get data for interest period
    foreach ($slides as $slide) {
      $options = $slide->getOptions();

      foreach ($options['resources'] as $resource) {
        try {
          $booking = $this->getBookingsForResource($resource['mail'], 'DEFAULT', $todayStart, $tomorrowStart);
          array_push($bookings, $booking);
        }
        catch (Exception $e) {
          // ignore.
        }
      }

      // Sort bookings by start time.

      // Save in calendarEvents field
      $slide->setCalendarEvents($bookings);
    }
  }
}
