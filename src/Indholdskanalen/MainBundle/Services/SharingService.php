<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indholdskanalen\MainBundle\Services;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerAware;
use Indholdskanalen\MainBundle\Events\SharingServiceEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class SharingService
 *
 * @package Indholdskanalen\MainBundle\Services
 */
class SharingService extends ContainerAware {
  protected $serializer;
  protected $container;
  protected $doctrine;
  protected $url;

  /**
   * Default constructor.
   *
   * @param Serializer $serializer
   * @param Container $container
   */
  function __construct(Serializer $serializer, Container $container) {
    $this->serializer = $serializer;
    $this->container = $container;

    $this->url = $this->container->getParameter('sharing_host') . $this->container->getParameter('sharing_path');
    $this->doctrine = $this->container->get('doctrine');
  }

  /**
   * onAddChannelToIndex event listener.
   * Add channel to index to external SharingService.
   *
   * @param SharingServiceEvent $event
   */
  public function onAddChannelToIndex(SharingServiceEvent $event) {
    $this->addChannelToIndex($event->getChannel(), $event->getSharingIndex());
  }

  /**
   * onRemoveChannelFromIndex event listener.
   * Remove channel from index from external SharingService.
   *
   * @param SharingServiceEvent $event
   */
  public function onRemoveChannelFromIndex(SharingServiceEvent $event) {
    $this->removeChannelFromIndex($event->getChannel(), $event->getSharingIndex());
  }

  /**
   * onUpdateChannel event listener.
   * Update channel in indexes in external SharingService.
   *
   * @param SharingServiceEvent $event
   */
  public function onUpdateChannel(SharingServiceEvent $event) {
    $this->updateChannel($event->getChannel(), $event->getSharingIndex());
  }

  /**
   * Add channel to index to external SharingService.
   *
   * @param $channel
   * @param $index
   */
  public function addChannelToIndex($channel, $index) {
    $params = array(
      'customer_id' => $index->getCustomerId(),
      'type' => 'Indholdskanalen\MainBundle\Entity\Channel',
      'id' => $channel->getSharingID(),
      'data' => $channel,
    );

    $data = $this->serializer->serialize($params, 'json', SerializationContext::create()->setGroups(array('sharing')));

    $this->curl($this->url, 'POST', $data);
  }

  /**
   * Remove channel from index from external SharingService.
   *
   * @param $channel
   * @param $index
   */
  public function removeChannelFromIndex($channel, $index) {
    $params = array(
      'customer_id' => $index->getCustomerId(),
      'type' => 'Indholdskanalen\MainBundle\Entity\Channel',
      'id' => $channel->getSharingID(),
      'data' => $channel,
    );

    $data = $this->serializer->serialize($params, 'json', SerializationContext::create()->setGroups(array('sharing')));

    $this->curl($this->url, 'DELETE', $data);
  }

  /**
   * Update channel in index in external SharingService.
   *
   * @param $channel
   * @param $index
   */
  public function updateChannel($channel, $index) {
    $params = array(
      'customer_id' => $index->getCustomerId(),
      'type' => 'Indholdskanalen\MainBundle\Entity\Channel',
      'id' => $channel->getSharingID(),
      'data' => $channel,
    );

    $data = $this->serializer->serialize($params, 'json', SerializationContext::create()->setGroups(array('sharing')));

    $this->curl($this->url, 'PUT', $data);
  }

  /**
   * Get channel from index on external SharingService.
   *
   * @param $channel_id
   * @param $index
   *
   * @return mixed
   */
  public function getChannelFromIndex($channel_id, $index) {
    $params = array(
      'customer_id' => $index,
      'type' => 'Indholdskanalen\MainBundle\Entity\Channel',
      'query' => array(
        'ids' => array(
          'values' => array(
            $channel_id
          )
        )
      )
    );

    $data = json_encode($params);

    $result = $this->curl($this->url . '/search', 'POST', $data);

    if ($result['status'] !== 200) {
      return false;
    }

    return $result['content'];
  }

  public function getAvailableSharingIndexes() {
    $result = $this->curl($this->url . '/indexes', 'GET', json_encode(array()));

    if ($result['status'] !== 200) {
      return array();
    }

    return $result['content'];
  }

  /**
   * Get all enabled sharing indexes.
   */
  public function getSharingIndexes() {
    $sharingIndexes = $this->doctrine->getRepository('IndholdskanalenMainBundle:SharingIndex')->findByEnabled(true);

    return $sharingIndexes;
  }

  /**
   * Save (new) sharing index
   *
   * @param IndholdskanalenMainBundle:SharingIndex $index
   */
  public function saveSharingIndex($index) {
    $doctrine = $this->container->get('doctrine');
    $em = $doctrine->getManager();

    $em->persist($index);
    $em->flush();
  }

  /**
   * Authenticate with the sharing service.
   *
   * @return bool
   */
  private function curlAuthenticate() {
    $apikey = $this->container->getParameter('sharing_apikey');
    $search_host = $this->container->getParameter('sharing_host');

    $jsonContent = json_encode(
      array(
        'apikey' => $apikey
      )
    );

    // Build, execute and close query.
    $ch = curl_init($search_host . "/authenticate");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonContent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_status === 200) {
      return json_decode($content)->token;
    }
    else {
      return false;
    }
  }

  /**
   * Get the authentication token from session else from Service.
   *
   * @return bool|mixed|null
   */
  public function sharingAuthenticate() {
    $session = new Session();
    $token = null;

    // If the token is set return it.
    if ($session->has('sharing_token')) {
      $token = $session->get('sharing_token');
    }
    else {
      $token = $this->curlAuthenticate();
      if ($token) {
        $session->set('sharing_token', $token);
      }
    }

    return $token;
  }

  /**
   * Remove session token and authenticate from Service.
   *
   * @return bool|mixed|null
   */
  public function sharingReauthenticate() {
    $session = new Session();
    $session->remove('sharing_token');

    $token = $this->curlAuthenticate();
    if ($token) {
      $session->set('sharing_token', $token);
    }

    return $token;
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
   *
   * @return array
   */
  protected function curl($url, $method = 'POST', $data) {
    // Get the authentication token.
    $token = $this->sharingAuthenticate();

    // Execute request.
    $ch = $this->buildQuery($url, $method, $token, $data);
    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // Close connection.
    curl_close($ch);

    // If unauthenticated, reauthenticate and retry.
    if ($http_status === 401) {
      $token = $this->sharingReauthenticate();

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
