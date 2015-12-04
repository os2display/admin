<?php
/**
 * @file
 * Contains the instagram service.
 */

namespace Indholdskanalen\MainBundle\Services;

use Guzzle\Http\Exception\BadResponseException;

/**
 * Class InstagramService
 * @package Indholdskanalen\MainBundle\Services
 */
class InstagramService {
  private $container;
  private $clientId;

  /**
   * Constructor.
   *
   * @param $container
   */
  public function __construct($container) {
    $this->container = $container;

    $this->clientId = $container->getParameter('instagram_client_id');
  }

  /**
   * Update the externalData for feed slides.
   */
  public function updateInstagramSlides() {
    $cache = array();

    $slides = $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:Slide')->findBySlideType('instagram');

    foreach ($slides as $slide) {
      $options = $slide->getOptions();

      $hashtag = $options['instagram_hashtag'];

      if (array_key_exists($hashtag, $cache)) {
        // Save in externalData field
        $slide->setExternalData($cache[$hashtag]);
      }
      else {
        $client = $this->container->get('guzzle.client');

        $numberOfItems = $options['instagram_number'];

        try {
          $response = $client->get('https://api.instagram.com/v1/tags/' . $hashtag . '/media/recent?client_id=' . $this->clientId . '&count=' . $numberOfItems)
            ->send();

          $data = json_decode($response->getBody());

          $res = array();

          foreach($data->data as $item) {
            $imageUrl = $item->images->standard_resolution->url;
            $imageUrl = preg_replace('/s(\d+)x(\d+)/', '', $imageUrl);

            $res[] = (object) array(
              'text' => $item->caption->text,
              'user' => (object) array(
                'username' => $item->user->username,
                'profile_picture' => $item->user->profile_picture,
              ),
              'image' => $imageUrl,
            );
          }

          // Cache the result for next iteration.
          $cache[$hashtag] = $res;

          // Save in externalData field
          $slide->setExternalData($res);
        }
        catch (BadResponseException $e) {
          $logger = $this->container->get('logger');
          $logger->warning('InstagramService: Unable to download ' . $hashtag);
          $logger->warning($e);

          // Ignore exceptions, and just leave the content that has already been stored.
        }
      }
    }

    $this->container->get('doctrine')->getManager()->flush();
  }
}
