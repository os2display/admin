<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indholdskanalen\MainBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class AuthenticationService
 *
 * @package Indholdskanalen\MainBundle\Services
 */
class AuthenticationService extends ContainerAware {
  protected $container;

  /**
   * Default constructor.
   *
   * @param Container $container
   */
  public function __construct(Container $container) {
    $this->container = $container;
  }

  /**
   * Authenticates against the host with the apiKey
   *
   * @param $host
   * @param $apiKey
   * @return array
   */
  private function authenticate($host, $apiKey) {
    // Build query.
    $ch = curl_init($host . '/authenticate');

    // SSL fix (self signed).
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS,
      json_encode(
        array(
          'apikey' => $apiKey
        )
      )
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));

    // Receive server response.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close connection.
    curl_close($ch);

    return array(
      'status' => $http_status,
      'content' => $content
    );
  }

  /**
   * Get the authorization token
   *
   * @param $prefix
   *   The name of the endpoint to authenticate against.
   * @param $reAuthenticate
   *   Whether or not to delete the token before authentication.
   *   Default: false
   *
   * @return array
   */
  public function getAuthentication($prefix, $reAuthenticate = false) {
    $session = new Session();
    $token = null;
    $tokenName = $prefix . '_token';

    if ($reAuthenticate) {
      $session->remove($tokenName);
    }

    // If the token is set return it.
    if (!$reAuthenticate && $session->has($tokenName)) {
      $token = $session->get($tokenName);

      $res = array(
        'status' => 200,
        'token' => $token
      );
    }
    else {
      $apiKey = $this->container->getParameter($prefix . '_apikey');
      $host = $this->container->getParameter($prefix . '_host');

      $auth = $this->authenticate($host, $apiKey);

      if ($auth['status'] === 200) {
        $token = json_decode($auth['content'])->token;

        $session->set($tokenName, $token);
      }

      $res = array(
        'status' => $auth['status'],
        'token' => $token
      );
    }

    return $res;
  }
}
