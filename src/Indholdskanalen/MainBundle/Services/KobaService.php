<?php
/**
 * @file
 * Contains the koba service.
 */

namespace Indholdskanalen\MainBundle\Services;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class KobaService
 * @package Indholdskanalen\MainBundle\Services
 */
class KobaService {
  private $apiKey;
  private $kobaPath;

  public function __construct($kobaPath, $apiKey) {
    $this->kobaPath = $kobaPath;
    $this->apiKey = $apiKey;
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

  public function getBookingsForResource($resourceMail, $group = 'DEFAULT') {
    $url = $this->kobaPath . '/api/resources/' . $resourceMail . '/group/' . $group . '/bookings?apikey=' . $this->apiKey;

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
}
