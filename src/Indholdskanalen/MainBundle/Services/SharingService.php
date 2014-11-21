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

/**
 * Class SharingService
 *
 * @package Indholdskanalen\MainBundle\Services
 */
class SharingService extends ContainerAware
{
  protected $serializer;
  protected $container;
  protected $url;

  /**
   * Default constructor.
   *
   * @param Serializer $serializer
   */
  function __construct(Serializer $serializer, Container $container) {
    $this->serializer = $serializer;
    $this->container = $container;

    $this->url = $this->container->getParameter('sharing_host') . $this->container->getParameter('sharing_path');
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
      'id' => $channel->getId(),
      'data' => $channel,
    );

    $this->curl($this->url, 'POST', $params);
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
      'id' => $channel->getId(),
      'data' => $channel,
    );

    $this->curl($this->url, 'DELETE', $params);
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
      'id' => $channel->getId(),
      'data' => $channel,
    );

    $this->curl($this->url, 'PUT', $params);
  }

  /**
   * Get channel from index on external SharingService.
   *
   * @param $channelID
   * @param $index
   *
   * @TODO: Implement!
   */
  public function getChannelFromIndex($channelID, $index) {

  }

  /**
   * Get all sharing indexes.
   */
  public function getSharingIndexes() {
    $doctrine = $this->container->get('doctrine');
    $sharingIndexes = $doctrine->getRepository('IndholdskanalenMainBundle:SharingIndex')->findAll();

    return $sharingIndexes;
  }

  /**
   * Save (new) sharing index
   */
  public function saveSharingIndex($index) {
    $doctrine = $this->container->get('doctrine');
    $em = $doctrine->getManager();

    $em->persist($index);
    $em->flush();
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
   * @param array $params
   *   The data to send.
   *
   * @return array
   */
  protected function curl($url, $method = 'POST', $params = array()) {
    // Build query.
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    $jsonContent = $this->serializer->serialize($params, 'json', SerializationContext::create()->setGroups(array('sharing')));

    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonContent);

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
      'content' => $content,
    );
  }
}
