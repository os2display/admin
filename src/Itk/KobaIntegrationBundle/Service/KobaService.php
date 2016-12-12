<?php
/**
 * @file
 * Contains the KOBA service.
 *
 * Provides integration service with KOBA.
 */

namespace Itk\KobaIntegrationBundle\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Indholdskanalen\MainBundle\Events\CronEvent;

/**
 * Class KobaService
 * @package Itk\KobaIntegrationBundle\Service
 */
class KobaService {
  private $container;
  private $apiKey;
  private $kobaPath;
  private $initialized;

  /**
   * Constructor.
   *
   * @param $container
   */
  public function __construct($container) {
    $this->container = $container;

    // Initialize if required parameters are set.
    if ($this->container->hasParameter('koba_path') && $this->container->hasParameter('koba_apikey')) {
      $this->initialized = TRUE;
      $this->kobaPath = $this->container->getParameter('koba_path');
      $this->apiKey = $this->container->getParameter('koba_apikey');
    }
  }

  /**
   * ik.onCron event listener.
   *
   * Updates calendar slides.
   *
   * @param CronEvent $event
   */
  public function onCron(CronEvent $event) {
    $this->updateCalendarSlides();
  }

  /**
   * Get resources by group id.
   *
   * @param string $group
   *
   * @return array
   */
  public function getResources($group = 'default') {
    // Only run if properly initialized.
    if (!$this->initialized) {
      throw new HttpException(501, 'Not supported.');
    }

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
    // Only run if properly initialized.
    if (!$this->initialized) {
      throw new HttpException(501, 'Not supported.');
    }

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
    // Only run if properly initialized.
    if (!$this->initialized) {
      return;
    }

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
        $interestInterval = 0;
        // Read interestInterval from options.
        if (isset($options['interest_interval'])) {
          $interestInterval = $options['interest_interval'];
        }
        $interestInterval = max(0, $interestInterval - 1);

        // Move today with number of requested days.
        $end = strtotime('+' . $interestInterval . ' days', $todayEnd);

        try{
          $resourceBookings = $this->getResourceBookings($resource['mail'], 'default', $todayStart, $end);

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

      // Save in externalData field
      $slide->setExternalData($bookings);

      $this->container->get('doctrine')->getManager()->flush();
    }
  }
}
