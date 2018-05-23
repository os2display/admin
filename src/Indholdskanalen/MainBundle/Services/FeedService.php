<?php
/**
 * @file
 * Contains the feed service.
 */

namespace Indholdskanalen\MainBundle\Services;

use Debril\RssAtomBundle\Exception\RssAtomException;
use Indholdskanalen\MainBundle\Events\CronEvent;

/**
 * Class FeedService
 * @package Indholdskanalen\MainBundle\Services
 */
class FeedService {
  private $container;
  private $entityManager;
  private $slideRepo;

  /**
   * Constructor.
   *
   * @param $container
   */
  public function __construct($container) {
    $this->container = $container;
    $this->entityManager = $this->container->get('doctrine')->getManager();
    $this->slideRepo = $container->get('doctrine')->getRepository('IndholdskanalenMainBundle:Slide');
  }

  /**
   * ik.onCron event listener.
   *
   * Updates feed slides.
   *
   * @param CronEvent $event
   */
  public function onCron(CronEvent $event) {
    $this->updateFeedSlides();
  }

  /**
   * Update the externalData for feed slides.
   */
  public function updateFeedSlides() {
    $cache = array();

    $slides = $this->slideRepo->findBySlideType('rss');

    foreach ($slides as $slide) {
      $options = $slide->getOptions();
      if (empty($options['source'])) {
        continue;
      } else {
        $source = $options['source'];
      }

      $md5Source = md5($source);

      // Check for previouslyDownloaded feed.
      if (array_key_exists($md5Source, $cache)) {
        // Save in externalData field
        $slide->setExternalData($cache[$md5Source]);

        $this->entityManager->flush();
      }
      else {
        // Fetch the FeedReader
        $reader = $this->container->get('debril.reader');

        try {
          // Fetch content
          $feed = $reader->getFeedContent($source);

          // Setup return array.
          $res = array(
            array(
              'feed' => array(),
              'title' => $feed->getTitle(),
            ),
          );

          // Get all items.
          $items = $feed->getItems();

          foreach ($items as $item) {
            $res[0]['feed'][] = array(
              'title' => $item->getTitle(),
              'date' => $item->getUpdated()->format('U'),
              'description' => $item->getDescription(),
            );
          }

          // Cache the result for next iteration.
          $cache[$md5Source] = $res;

          // Save in externalData field
          $slide->setExternalData($res);

          $this->entityManager->flush();
        }
        catch (\Exception $e) {
          $logger = $this->container->get('logger');
          $logger->warning('FeedService: Error while fetching data for slide id ' . $slide->getId());
          $logger->warning('FeedService: Unable to download feed from ' . $source);
          $logger->warning($e);

          // Ignore exceptions, and just leave the content that has already been stored.
        }
      }
    }
  }
}
