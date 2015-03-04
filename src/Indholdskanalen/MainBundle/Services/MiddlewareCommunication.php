<?php
/**
 * @file
 * This file is a part of the IndholdskanalenMainBundle.
 *
 * Contains the middleware communication service.
 */

namespace Indholdskanalen\MainBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;
use Indholdskanalen\MainBundle\Services\UtilityService;
use Indholdskanalen\MainBundle\Entity\Channel;
use Indholdskanalen\MainBundle\Entity\SharedChannel;

/**
 * Class MiddlewareCommunication
 *
 * @package Indholdskanalen\MainBundle\Services
 */
class MiddlewareCommunication extends ContainerAware {
  protected $templateService;
  protected $utilityService;

  // With what interval should the push be forced through?
  private $forcePushInterval = 3600;

  /**
   * Constructor.
   *
   * @param TemplateService $templateService
   *   The template service.
   * @param UtilityService $utilityService
   *   The utility service.
   */
  public function __construct(TemplateService $templateService, UtilityService $utilityService) {
    $this->templateService = $templateService;
    $this->utilityService = $utilityService;
  }

  /**
   * Push a Channel or a SharedChannel to the middleware.
   *
   * @param Channel|SharedChannel $channel
   *   The Channel or SharedChannel to push.
   * @param mixed $data
   *   The Data that should be pushed for $channel encoded as json.
   * @param string $id
   *   The id of the channel (internal id for Channel, unique_id for SharedChannel)
   * @param boolean $force
   *   Should the push be forced through?
   */
  public function pushChannel($channel, $data, $id, $force) {
    $doctrine = $this->container->get('doctrine');
    $em = $doctrine->getManager();

    // Calculate hash of content, used to avoid unnecessary push.
    $sha1 = sha1($data);

    // Get current time.
    $time = time();

    // Get time of last push for the channel.
    $lastPushTime = $channel->getLastPushTime();

    $middlewarePath = $this->container->getParameter("middleware_host") . $this->container->getParameter("middleware_path");

    // Check if the channel should be pushed.
    if ($force || $sha1 !== $channel->getLastPushHash() || $lastPushTime === NULL || $time - $lastPushTime > $this->forcePushInterval) {
      $curlResult = $this->utilityService->curl(
        $middlewarePath . "/channel/" . $id,
        'POST',
        $data,
        'middleware'
      );

      // If the result was delivered, update the last hash.
      if ($curlResult['status'] === 200) {
        $lastPushScreens = $channel->getLastPushScreens();

        // Get screen ids.
        $regions = $channel->getChannelScreenRegions();
        $screenIds = array();
        foreach ($regions as $region) {
          if (!in_array($region->getScreen()->getId(), $screenIds)) {
            $screenIds[] = $region->getScreen()->getId();
          }
        }

        // Push deletes to the middleware if a channel has been on a screen previously,
        //   but now has been removed.
        $deleteSuccess = TRUE;
        foreach (json_decode($lastPushScreens) as $lastPushScreenId) {
          if (!in_array($lastPushScreenId, $screenIds)) {
            $curlResult = $this->utilityService->curl(
              $middlewarePath . "/channel/" . $id . "/screen/" . $lastPushScreenId,
              'DELETE',
              json_encode(array()),
              'middleware'
            );

            if ($curlResult['status'] !== 200) {
              $deleteSuccess = FALSE;
            }
          }
        }

        // If the delete process was successful, update last push information.
        //   else set values to NULL to ensure new push.
        if ($deleteSuccess) {
          $channel->setLastPushScreens(json_encode($screenIds));
          $channel->setLastPushHash($sha1);
          $channel->setLastPushTime($time);
        }
        else {
          $channel->setLastPushHash(NULL);
          $channel->setLastPushTime(NULL);
        }
      }
      else {
        $channel->setLastPushHash(NULL);
        $channel->setLastPushTime(NULL);
      }
      $em->flush();
    }
  }

  /**
   * Pushes the channels for each screen to the middleware.
   *
   * @param boolean $force
   *   Should the push to screen be forced, even though the content has previously been pushed to the middleware?
   */
  public function pushToScreens($force = FALSE) {
    // Get doctrine handle
    $doctrine = $this->container->get('doctrine');

    $serializer = $this->container->get('jms_serializer');

    // Push channels
    $channels = $doctrine->getRepository('IndholdskanalenMainBundle:Channel')
      ->findAll();

    foreach ($channels as $channel) {
      $data = $serializer->serialize($channel, 'json', SerializationContext::create()
          ->setGroups(array('middleware')));

      $this->pushChannel($channel, $data, $channel->getId(), $force);
    }

    // Push shared channels
    $sharedChannels = $doctrine->getRepository('IndholdskanalenMainBundle:SharedChannel')
      ->findAll();

    foreach ($sharedChannels as $sharedChannel) {
      $data = $serializer->serialize($sharedChannel, 'json', SerializationContext::create()
          ->setGroups(array('middleware')));

      // Hack to get slides encoded correctly
      //   Issue with how the slides array is encoded in jms_serializer.
      $d = json_decode($data);
      $d->data->slides = json_decode($d->data->slides);
      $data = json_encode($d);

      if ($data === NULL) {
        continue;
      }

      $this->pushChannel($sharedChannel, $data, $sharedChannel->getUniqueId(), $force);
    }
  }
}
