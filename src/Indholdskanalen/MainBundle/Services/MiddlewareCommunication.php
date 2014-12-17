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

  function __construct(TemplateService $templateService, UtilityService $utilityService) {
    $this->templateService = $templateService;
    $this->utilityService = $utilityService;
  }

  /**
   * Send the screen to the middleware.
   *
   * @param $screen
   * @return boolean
   */
  protected function curlSendContent($screen) {
    // Send  post request to middleware (/push/channel).
    $url = $this->container->getParameter("middleware_host") . "/push/channel";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $screen);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-type: application/json',
      'Content-Length: ' . strlen($screen),
    ));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    if (!$result = curl_exec($ch)) {
      $logger = $this->container->get('logger');
      $logger->error(curl_error($ch));
    }

    curl_close($ch);

    return $result;
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
    $em = $doctrine->getManager();
    $serializer = $this->container->get('jms_serializer');

    $channels = $doctrine->getRepository('IndholdskanalenMainBundle:Channel')->findAll();

    foreach ($channels as $channel) {
      $jsonContent = $serializer->serialize($channel, 'json', SerializationContext::create()->setGroups(array('middleware')));

      // Calculate hash of content, used to avoid unnecessary push.
      $sha1 = sha1($jsonContent);

      if ($force || $sha1 !== $channel->getLastPushHash()) {
        $curlResult = $this->utilityService->curl(
          $this->container->getParameter("middleware_host") . $this->container->getParameter("middleware_path") . "/channel/" . $channel->getId() . "/push",
          'POST',
          $jsonContent,
          'middleware'
        );

        // If the result was delivered, update the last hash.
        if ($curlResult) {
          $channel->setLastPushHash($sha1);
          $em->flush();
        }
        else {
          $channel->setLastPushHash(null);
          $em->flush();
        }

        print_r($curlResult);
      }
    }

    /*
    // Get all screens
    $screens = $doctrine->getRepository('IndholdskanalenMainBundle:Screen')->findAll();

    foreach($screens as $screen) {
      $serializer = $this->container->get('jms_serializer');
      $jsonContent = $serializer->serialize($screen, 'json', SerializationContext::create()->setGroups(array('middleware')));

      // Add shared channels.
      // @TODO: find a more pretty solution than decode/encode.
      $content = json_decode($jsonContent);
      foreach($screen->getSharedChannels() as $sharedChannel) {
        $channel = $sharedChannel->getContent();
        if ($channel === null) {
          continue;
        }

        $channel = json_decode($channel);
        foreach($channel->slides as $slide) {
          $content->channelContent->slides[] = $slide;
        }
      }
      $jsonContent = json_encode($content);

      // Calculate hash of content, used to avoid unnecessary push.
      $sha1 = sha1($jsonContent);

      if ($force || $sha1 !== $screen->getLastPushHash()) {
        $curlResult = $this->curlSendContent($jsonContent);

        // If the result was delivered, update the last hash.
        if ($curlResult) {
          $screen->setLastPushHash($sha1);
          $em->flush();
        }
        else {
          $screen->setLastPushHash(null);
          $em->flush();
        }
      }
    }*/
  }
}
