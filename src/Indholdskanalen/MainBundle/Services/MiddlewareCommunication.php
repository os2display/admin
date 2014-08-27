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

/**
 * Class MiddlewareCommunication
 *
 * @package Indholdskanalen\MainBundle\Services
 */
class MiddlewareCommunication extends ContainerAware
{
  protected function curlSendChannel($channel) {
    $json = json_encode($channel);

    // Send  post request to middleware (/push/channel).
    $url = $this->container->getParameter("middleware_host") . "/push/channel";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-type: application/json',
      'Content-Length: ' . strlen($json),
    ));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    if (!$result = curl_exec($ch)) {
      $logger = $this->container->get('logger');
      $logger->error(curl_error($ch));
    }

    curl_close($ch);
  }

  /**
   * Pushes the channels to the middleware
   */
  public function pushChannels()
  {
    // Get all channels
    $channels = $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:Channel')->findAll();

    // For each channel
    for ($i = 0; $i < count($channels); $i++) {
      $currentChannel = $channels[$i];

      // Build default channel array.
      $channel = array(
        'channelID' => $currentChannel->getId(),
        'channelContent' => array(
          'logo' => '',
        ),
        'groups' => array(
          'fisk',
        ),
      );

      //   Add slides to the channel
      $slides = $currentChannel->getSlides();
      foreach ($slides as $key => $value) {
        $slide = $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:Slide')->findOneById($value);

        $channel['channelContent']['slides'][] = array(
          'id' => $slide->getId(),
          'title'   => $slide->getTitle(),
          'orientation' => $slide->getOrientation(),
          'template' => $slide->getTemplate(),
          'options' => $slide->getOptions(),
        );
      }

      //   Set the groups it should be shown in
      $this->curlSendChannel($channel);
    }
  }
}