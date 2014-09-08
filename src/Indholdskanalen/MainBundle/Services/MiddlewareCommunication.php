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
   * Pushes the channels to the middleware.
   */
  public function pushChannels() {
    // Get all channels
    $channels = $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:Channel')->findAll();

    // Path to server
    $pathToServer = $this->container->getParameter("absolute_path_to_server");

    // Get handle to media.
    $sonataMedia = $this->container->get('doctrine')->getRepository('ApplicationSonataMediaBundle:Media');

    // For each channel
    $count = count($channels);
    for ($i = 0; $i < $count; $i++) {
      $currentChannel = $channels[$i];

      // Create groups array.
      $groups = array();
      foreach($currentChannel->getScreens() as $screen) {
        $groups[] = "group" . $screen->getId();
      }

      // Build default channel array.
      $channel = array(
        'channelID' => $currentChannel->getId(),
        'groups' => $groups,
        'channelContent' => array(
          'logo' => '',
        ),
      );

      // Add slides to the channel
      $slides = $currentChannel->getSlides();
      foreach ($slides as $key => $value) {
        $slide = $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:Slide')->findOneById($value);

        // Build image urls.
        $imageUrls = array();
        foreach($slide->getOptions()['images'] as $imageId) {
          $image = $sonataMedia->findOneById($imageId);

          if ($image) {
            $path = $this->container->get('sonata.media.twig.extension')->path($image, 'reference');
            $imageUrls[$imageId] = $pathToServer . $path;
          }
        }

        $channel['channelContent']['slides'][] = array(
          'id' => $slide->getId(),
          'title'   => $slide->getTitle(),
          'orientation' => $slide->getOrientation(),
          'template' => $slide->getTemplate(),
          'options' => $slide->getOptions(),
          'imageUrls' => $imageUrls,
          'duration' => $slide->getDuration(),
        );
      }

      //   Set the groups it should be shown in
      $this->curlSendChannel($channel);
    }
  }
}
