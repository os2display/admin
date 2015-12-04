<?php
/**
 * @file
 * Contains the instagram service.
 */

namespace Indholdskanalen\MainBundle\Services;

/**
 * Class InstagramService
 * @package Indholdskanalen\MainBundle\Services
 */
class InstagramService {
  private $container;

  /**
   * Constructor.
   *
   * @param $container
   */
  public function __construct($container) {
    $this->container = $container;
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
        $slide->setExternalData($cache[]);
      }
      else {

      }

      //"https://api.instagram.com/v1/tags/" + slide.options.instagram_hashtag + "/media/recent?callback=JSON_CALLBACK&client_id=" + slide.clientId + "&count=" + slide.options.instagram_number)

    }

    $this->container->get('doctrine')->getManager()->flush();
  }
}
