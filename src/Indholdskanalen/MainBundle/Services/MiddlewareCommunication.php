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
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;
use Indholdskanalen\MainBundle\Services\UtilityService;

/**
 * Class MiddlewareCommunication
 *
 * @package Indholdskanalen\MainBundle\Services
 */
class MiddlewareCommunication extends ContainerAware
{
  protected $templateService;
  protected $utilityService;

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
   * Push a channel to the middleware.
   *
   * @param $channel
   * @param $data
   * @param $id
   * @param $force
   */
  public function pushChannel($channel, $data, $id, $force) {
    $doctrine = $this->container->get('doctrine');
    $em = $doctrine->getManager();

    // Calculate hash of content, used to avoid unnecessary push.
    $sha1 = sha1($data);

    if ($force || $sha1 !== $channel->getLastPushHash()) {
      $curlResult = $this->utilityService->curl(
        $this->container->getParameter("middleware_host") . $this->container->getParameter("middleware_path") . "/channel/" . $id,
        'POST',
        $data,
        'middleware'
      );

      // If the result was delivered, update the last hash.
      if ($curlResult['status'] === 200) {
        $lastPushScreens = $channel->getLastPushScreens();
        $screens = $channel->getScreens();
        $ids = array();

        foreach($screens as $screen) {
          $ids[] = $screen->getId();
        }

        $deleteSuccess = true;

        foreach (json_decode($lastPushScreens) as $lp_id) {
          if (!in_array($lp_id, $ids)) {
            $curlResult = $this->utilityService->curl(
              $this->container->getParameter("middleware_host") . $this->container->getParameter("middleware_path") .
              "/channel/" . $id . "/screen/" . $lp_id,
              'DELETE',
              json_encode(array()),
              'middleware'
            );

            if ($curlResult['status'] !== 200) {
              $deleteSuccess = false;
            }
          }
        }

        if ($deleteSuccess) {
          $channel->setLastPushScreens(json_encode($ids));
        }
        $channel->setLastPushHash($sha1);
      }
      else {
        $channel->setLastPushHash(null);
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
  public function pushToScreens($force = false) {
    // Get doctrine handle
    $doctrine = $this->container->get('doctrine');

    $serializer = $this->container->get('jms_serializer');

    // Push channels
    $channels = $doctrine->getRepository('IndholdskanalenMainBundle:Channel')->findAll();
    foreach ($channels as $channel) {
      $data = $serializer->serialize($channel, 'json', SerializationContext::create()->setGroups(array('middleware')));

      $this->pushChannel($channel, $data, $channel->getId(), $force);
    }

    // Push shared channels
    $sharedChannels = $doctrine->getRepository('IndholdskanalenMainBundle:SharedChannel')->findAll();
    foreach($sharedChannels as $sharedChannel) {
      $data = $serializer->serialize($sharedChannel, 'json', SerializationContext::create()->setGroups(array('middleware')));

      if ($data === null) {
        continue;
      }

      $this->pushChannel($sharedChannel, $data, $sharedChannel->getUniqueId(), $force);
    }
  }
}
