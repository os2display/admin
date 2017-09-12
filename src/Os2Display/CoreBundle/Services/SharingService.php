<?php
/**
 * @file
 * This file is a part of the Os2Display CoreBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Os2Display\CoreBundle\Services;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Os2Display\CoreBundle\Events\SharingServiceEvent;
use Symfony\Component\DependencyInjection\Container;
use Os2Display\CoreBundle\Events\CronEvent;

/**
 * Class SharingService
 *
 * @package Os2Display\CoreBundle\Services
 */
class SharingService {
  protected $utilityService;
  protected $serializer;
  protected $container;
  protected $doctrine;
  protected $url;

  /**
   * Constructor.
   *
   * @param Serializer $serializer
   * @param Container $container
   * @param UtilityService $utilityService
   */
  public function __construct(Serializer $serializer, Container $container, UtilityService $utilityService) {
    $this->serializer = $serializer;
    $this->container = $container;
    $this->utilityService = $utilityService;

    $this->url = $this->container->getParameter('sharing_host') . $this->container->getParameter('sharing_path');
    $this->doctrine = $this->container->get('doctrine');
  }

  /**
   * ik.onCron event listener.
   *
   * Updates shared channels.
   *
   * @param CronEvent $event
   */
  public function onCron(CronEvent $event) {
    if ($this->container->getParameter('sharing_enabled')) {
      $this->updateAllSharedChannels();
    }
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
   *
   * @return array
   */
  public function addChannelToIndex($channel, $index) {
    $params = array(
      'index' => $index->getIndex(),
      'type' => 'Os2Display\CoreBundle\Entity\Channel',
      'id' => $channel->getUniqueId(),
      'data' => $channel,
    );

    $data = $this->serializer->serialize($params, 'json', SerializationContext::create()->setGroups(array('sharing')));

    return $this->utilityService->curl($this->url, 'POST', $data, 'sharing');
  }

  /**
   * Remove channel from index from external SharingService.
   *
   * @param $channel
   * @param $index
   *
   * @return array
   */
  public function removeChannelFromIndex($channel, $index) {
    $params = array(
      'index' => $index->getIndex(),
      'type' => 'Os2Display\CoreBundle\Entity\Channel',
      'id' => $channel->getUniqueId()
    );

    $data = $this->serializer->serialize($params, 'json', SerializationContext::create()->setGroups(array('sharing')));

    return $this->utilityService->curl($this->url, 'DELETE', $data, 'sharing');
  }

  /**
   * Update channel in index in external SharingService.
   *
   * @param $channel
   * @param $index
   *
   * @return array
   */
  public function updateChannel($channel, $index) {
    $params = array(
      'index' => $index->getIndex(),
      'type' => 'Os2Display\CoreBundle\Entity\Channel',
      'id' => $channel->getUniqueId(),
      'data' => $channel,
    );

    $data = $this->serializer->serialize($params, 'json', SerializationContext::create()->setGroups(array('sharing')));

    return $this->utilityService->curl($this->url, 'PUT', $data, 'sharing');
  }

  /**
   * Get channel from index on external SharingService.
   *
   * @param $channelId
   *   Id of the channel.
   * @param $index
   *   The index to get channel from.
   *
   * @return mixed
   */
  public function getChannelFromIndex($channelId, $index) {
    $params = array(
      'index' => $index,
      'type' => 'Os2Display\CoreBundle\Entity\Channel',
      'id' => $channelId,
      'query' => array(
        'query' => array(
          'ids' => array(
            'values' => array(
              $channelId
            )
          )
        )
      )
    );

    $data = json_encode($params);

    return $this->utilityService->curl($this->url . '/search', 'POST', $data, 'sharing');
  }

  /**
   * Update the content of all shared channels.
   * For CRON process.
   */
  public function updateAllSharedChannels() {
    $sharedChannels = $this->doctrine->getRepository('Os2DisplayCoreBundle:SharedChannel')->findAll();
    $em = $this->doctrine->getManager();

    foreach($sharedChannels as $sharedChannel) {
      $content = $this->getChannelFromIndex($sharedChannel->getUniqueId(), $sharedChannel->getIndex());

      if ($content['status'] === 200) {
        $result = json_decode($content['content']);
        if ($result->hits > 0) {
          $sharedChannel->setContent(json_encode($result->results[0]));
        }
        else {
          $em->remove($sharedChannel);
        }
      }
    }

    $em->flush();
  }

  public function getAvailableSharingIndexes() {
    $result = $this->utilityService->curl($this->url . '/indexes', 'GET', json_encode(array()), 'sharing');

    if ($result['status'] !== 200) {
      return array();
    }

    return $result['content'];
  }

  /**
   * Get all enabled sharing indexes.
   */
  public function getSharingIndexes() {
    $sharingIndexes = $this->doctrine->getRepository('Os2DisplayCoreBundle:SharingIndex')->findByEnabled(true);

    return $sharingIndexes;
  }

  /**
   * Save (new) sharing index
   *
   * @param Os2DisplayCoreBundle:SharingIndex $index
   */
  public function saveSharingIndex($index) {
    $doctrine = $this->container->get('doctrine');
    $em = $doctrine->getManager();

    $em->persist($index);
    $em->flush();
  }
}
