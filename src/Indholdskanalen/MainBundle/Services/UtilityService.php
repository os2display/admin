<?php

namespace Indholdskanalen\MainBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use Indholdskanalen\MainBundle\Services\AuthenticationService;

class UtilityService extends ContainerAware {
  protected $authenticationService;

  /**
   * Constructor.
   *
   * @param AuthenticationService $authenticationService
   */
  function __construct(AuthenticationService $authenticationService) {
    $this->authenticationService = $authenticationService;
  }

  /**
   * Helper method: build the curl query.
   *
   * @param $url
   * @param $method
   * @param $token
   * @param $data
   * @return resource
   */
  private function buildQuery($url, $method, $token, $data) {
    // Build query.
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $token
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    return $ch;
  }

  /**
   * Communication function.
   *
   * Wrapper function for curl to send data to ES.
   *
   * @param $url
   *   URL to connect to.
   * @param string $method
   *   Method to send/get data "POST" or "PUT".
   * @param array $data
   *   The data to send.
   * @param string $prefix
   *   The authentication prefix.
   *
   * @return array
   */
  public function curl($url, $method = 'POST', $data, $prefix) {
    $token = $this->authenticationService->getAuthentication($prefix);

    // Execute request.
    $ch = $this->buildQuery($url, $method, $token, $data);
    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // Close connection.
    curl_close($ch);

    // If unauthenticated, reauthenticate and retry.
    if ($http_status === 401) {
      $token = $token = $this->authenticationService->getAuthentication($prefix, true);

      // Execute.
      $ch = $this->buildQuery($url, $method, $token, $data);
      $content = curl_exec($ch);
      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      // Close connection.
      curl_close($ch);
    }

    return array(
      'status' => $http_status,
      'content' => $content,
    );
  }
}