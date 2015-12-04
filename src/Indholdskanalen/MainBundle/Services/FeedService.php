<?php
/**
 * @file
 * Contains the feed service.
 */

namespace Indholdskanalen\MainBundle\Services;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class FeedService
 * @package Indholdskanalen\MainBundle\Services
 */
class FeedService {
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
   * Update the calendar events for calendar slides.
   */
  public function updateFeedSlides() {
    // @TODO: Cache results.

    $slides = $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:Slide')->findBySlideType('rss');

    foreach ($slides as $slide) {
      $data = array();

      $options = $slide->getOptions();

      $source = $options['source'];

      // fetch the FeedReader
      $reader = $this->container->get('debril.reader');

      // now fetch its (fresh) content
      $feed = $reader->getFeedContent($source);

      $res = array(
        "feed" => array(),
        "title" => $feed->getTitle(),
      );

      // the $content object contains as many Item instances as you have fresh articles in the feed
      $items = $feed->getItems();

      foreach ($items as $item) {
        $res["feed"][] = (object) array(
          "title" => $item->getTitle(),
          "date" => $item->getUpdated()->format('U'),
          "description" => $item->getDescription(),
        );
      }

      // Save in externalData field
      $slide->setExternalData($res);

      $this->container->get('doctrine')->getManager()->flush();
    }
  }
}
