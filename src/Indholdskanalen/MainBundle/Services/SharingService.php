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
  protected $authenticationService;
  protected $serializer;
  protected $container;
  protected $doctrine;
  protected $url;

  /**
   * Constructor.
   *
   * @param Serializer $serializer
   * @param Container $container
   * @param AuthenticationService $authenticationService
   */
  function __construct(Serializer $serializer, Container $container, AuthenticationService $authenticationService) {
    $this->serializer = $serializer;
    $this->container = $container;
    $this->authenticationService = $authenticationService;

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
      'index' => $index->getIndex(),
      'type' => 'Indholdskanalen\MainBundle\Entity\Channel',
      'id' => $channel->getUniqueId(),
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
      'index' => $index->getIndex(),
      'type' => 'Indholdskanalen\MainBundle\Entity\Channel',
      'id' => $channel->getUniqueId(),
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
      'index' => $index->getIndex(),
      'type' => 'Indholdskanalen\MainBundle\Entity\Channel',
      'id' => $channel->getUniqueId(),
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
      'index' => $index,
      'type' => 'Indholdskanalen\MainBundle\Entity\Channel',
      'id' => $channel_id,
      'query' => array(
        'query' => array(
          'ids' => array(
            'values' => array(
              $channel_id
            )
          )
        )
      )
    );

    $data = json_encode($params);

    $result = $this->curl($this->url . '/search', 'POST', $data);

    return $result;
  }

  /**
   * Update the content of all shared channels.
   * For CRON process.
   */
  public function updateAllSharedChannels() {
    $sharedChannels = $this->doctrine->getRepository('IndholdskanalenMainBundle:SharedChannel')->findAll();
    $em = $this->doctrine->getManager();

    foreach($sharedChannels as $sharedChannel) {
      $content = $this->getChannelFromIndex($sharedChannel->getUniqueId(), $sharedChannel->getIndex());

      if ($content['status'] === 200) {
        $result = json_decode($content['content']);
        if ($result->hits > 0) {
          $sharedChannel->setContent(json_encode($result->results[0]));
        }
      }
      else if ($content['status'] === 404) {
        // Channel removed, remove content of from db.
        $sharedChannel->setContent(null);
      }
    }

    $em->flush();
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
    $token = $this->authenticationService->getAuthentication('sharing');

    // Execute request.
    $ch = $this->buildQuery($url, $method, $token, $data);
    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // Close connection.
    curl_close($ch);

    // If unauthenticated, reauthenticate and retry.
    if ($http_status === 401) {
      $token = $token = $this->authenticationService->getAuthentication('sharing', true);

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
