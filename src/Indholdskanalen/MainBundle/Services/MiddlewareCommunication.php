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
   * Pushes the channels for each screen to the middleware.
   */
  public function pushChannels() {
    // Get doctrine handle
    $doctrine = $this->container->get('doctrine');

    // Path to server
    $pathToServer = $this->container->getParameter("absolute_path_to_server");

    // Get all screens
    $screens = $doctrine->getRepository('IndholdskanalenMainBundle:Screen')->findAll();

    // Get handle to media.
    $sonataMedia = $doctrine->getRepository('ApplicationSonataMediaBundle:Media');

    // For each screen
    //   Join channels
    //   Push joined channels to the screen
    foreach($screens as $screen) {
      $slides = array();
      foreach($screen->getChannels() as $channel) {
        $channelSlideOrders = $channel->getChannelSlideOrders();
        foreach($channelSlideOrders as $channelSlideOrder) {
          $slide = $channelSlideOrder->getSlide();

          if (!$slide) {
            continue;
          }

          // Build image urls.
          $imageUrls = array();
          if (isset($slide->getOptions()['images'])) {
            foreach($slide->getOptions()['images'] as $imageId) {
              $image = $sonataMedia->findOneById($imageId);

              if ($image) {
                $path = $this->container->get('sonata.media.twig.extension')->path($image, 'reference');
                $imageUrls[$imageId] = $pathToServer . $path;
              }
            }
          }

          // Build image urls.
          $videoUrls = array();
          if (isset($slide->getOptions()['videos'])) {
            foreach($slide->getOptions()['videos'] as $videoId) {
              $video = $sonataMedia->findOneById($videoId);

              $serializer = $this->container->get('jms_serializer');
              $jsonContent = $serializer->serialize($video, 'json');

              $content = json_decode($jsonContent);

              if ($video) {
                $urls = array(
                  'mp4' => $pathToServer . $content->provider_metadata[0]->reference,
                  'ogg' => $pathToServer . $content->provider_metadata[1]->reference,
                );

                $videoUrls[$videoId] = $urls;
              }
            }
          }

          // Build slide entry
          $slideEntry = array(
            'id' => $slide->getId(),
            'title'   => $slide->getTitle(),
            'orientation' => $slide->getOrientation(),
            'template' => $slide->getTemplate(),
            'options' => $slide->getOptions(),
            'imageUrls' => $imageUrls,
            'videoUrls' => $videoUrls,
            'duration' => $slide->getDuration(),
          );

          $slides[] = $slideEntry;
        }
      }

      // Build default screen array.
      $screenArray = array(
        'channelID' => "group" . $screen->getId(),
        'groups' => array(
          "group" .$screen->getId()
        ),
        'channelContent' => array(
          'logo' => '',
          'slides' => $slides,
        ),
      );

      $this->curlSendChannel($screenArray);
    }
  }
}
