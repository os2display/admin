<?php
/**
 * @file
 * Service for KK Os2Display.
 */

namespace Kkos2\KkOs2DisplayIntegrationBundle\Service;

use Kkos2\KkOs2DisplayIntegrationBundle\Crawlers\MellemfeedCrawler;
use Kkos2\KkOs2DisplayIntegrationBundle\Crawlers\ServicespotCrawler;
use Kkos2\KkOs2DisplayIntegrationBundle\Events\KultunautFeedParser;
use Os2Display\CoreBundle\Entity\Slide;
use Os2Display\CoreBundle\Events\CronEvent;

class Kkos2DisplayService
{
  /**
   * @var \Symfony\Bridge\Monolog\Logger $logger
   */
  private $logger;
  /**
   * @var \Symfony\Component\DependencyInjection\ContainerInterface $container
   */
  private $container;

  /**
   * Low-tech caching of mellemfeed links.
   * @var array $mellemfeedLinks
   */
  private $mellemfeedLinks = [];

  /**
   * Kkos2DisplayService constructor.
   * @param $container
   */
  public function __construct($container)
  {
    $this->container = $container;
    $this->logger = $this->container->get('logger');
  }

  /**
   * ik.onCron event listener.
   *
   * @param CronEvent $event
   */
  public function onCron(CronEvent $event)
  {
    // Make sure our dates are formatted with Danish locale
    $oldLocale = setlocale(LC_ALL, 0);
    setlocale(LC_ALL, 'da_DK.UTF-8');
    $slideRepo = $this->container->get('doctrine')->getRepository('Os2DisplayCoreBundle:Slide');

    // Update colorful messages.
    $colorMessageSlides = $slideRepo->findBySlideType('color-messages');
    array_map([$this, 'updateColorMessageSlide'], $colorMessageSlides);

    // Update eventplakat slides.
    $plakatSlides = $slideRepo->findBySlideType('eventplakat');
    array_map([$this, 'UpdateEventPlakatSlide'], $plakatSlides);

    // Update events.
    $eventSlides = $slideRepo->findBySlideType('kultunaut-event');
    array_map([$this, 'updateKultunautEventsSlide'], $eventSlides);

    // Set locale back to what it was.
    setlocale(LC_ALL, $oldLocale);
  }

  /**
   * Update a color message slide with data.
   *
   * @param Slide $slide
   *   Slide to update.
   */
  private function updateColorMessageSlide(Slide $slide)
  {
    $options = $slide->getOptions();
    if (empty($options['mellemfeed'])) {
      return;
    }

    // Get service spots to scrape from the "temporary" mellemfeed solution.
    $servicespotLinks = $this->getMellemfeedLinksForGroup($options['mellemfeed'], 'servicespots');

    // Get data for each message the slide will display.
    $messages = array_reduce($servicespotLinks, function ($carry, $link) {
      try {
        $crawler = new ServicespotCrawler($link);
        $crawler->crawl();

        $message = $crawler->getContent();
        if (!empty($message)) {
          $carry[] = $message;
        }
      } catch (\Exception $e) {
        $this->logger->error($e->getMessage());
      }
      return $carry;
    }, []);

    $externalData = [
      'messages' => $messages,
    ];
    $this->logger->addError(print_r($externalData, true));
    $slide->setExternalData($externalData);
    $entityManager = $this->container->get('doctrine')->getManager();
    $entityManager->flush();
  }

  private function UpdateEventPlakatSlide(Slide $slide)
  {
    $options = $slide->getOptions();
    if (empty($options['mellemfeed'])) {
      return;
    }

    // Get service spots to scrape from the "temporary" mellemfeed solution.
    $plakatLinks = $this->getMellemfeedLinksForGroup($options['mellemfeed'], 'plakater');
    $events = [];
    try {
      $parser = new KultunautFeedParser($options['kultunautfeed']);
      $parser->parse();
      $events = $parser->getEventsWithUrls($plakatLinks);
    }
    catch (\Exception $O_o) {
      $this->logger->error('An error occured trying to update eventplakat slide: ' . $O_o->getMessage());
    }

    $externalData = [
      'plakat_slides' => $events,
      'num_slides' => count($events),
    ];
    $this->logger->addError(print_r($externalData, true));
    try {
      $slide->setExternalData($externalData);
      // Write to the db.
      $entityManager = $this->container->get('doctrine')->getManager();
      $entityManager->flush();
    } catch (\Exception $O_o) {
      $this->logger->error('An error occured trying save data on plakatevent slide: ' . $O_o->getMessage());
    }
  }

  /**
   * Update an event slide.
   *
   * @param Slide $slide
   *   Slide to update.
   * @throws \Exception
   *   If there was a problem updating.
   */
  private function updateKultunautEventsSlide(Slide $slide)
  {
    $options = $slide->getOptions();
    if (empty($options['kultunautfeed'])) {
      return;
    }

    // Hardcode this number for now.
    $eventsPrSlide = 3;
    $upcoming = [];
    try {
      $parser = new KultunautFeedParser($options['kultunautfeed']);
      $parser->parse();
      $upcoming = $parser->getUpcoming($options['rss_number']);
    }
    catch (\Exception $O_o) {
      $this->logger->error('An error occured trying to update event slide: ' . $O_o->getMessage());
    }
    $externalData = [
      'slides' => array_chunk($upcoming, $eventsPrSlide),
      'num_slides' => count($upcoming),
    ];
    try {
      $slide->setExternalData($externalData);
      // Write to the db.
      $entityManager = $this->container->get('doctrine')->getManager();
      $entityManager->flush();
    } catch (\Exception $O_o) {
      $this->logger->error('An error occured trying save data on event slide: ' . $O_o->getMessage());
    }
  }

  /**
   * Get links for a group from the mellemfeed.
   *
   * @param string $mellemfeedUrl
   *   Link to the KK multisite mellemfeed.
   * @param string $group
   *   The name of the grouping in the mellemfeed to get links from.
   *
   * @return array
   *   Array of links from group in mellemfeed.
   */
  private function getMellemfeedLinksForGroup($mellemfeedUrl, $group)
  {
    if (empty($this->mellemfeedLinks[$mellemfeedUrl])) {
      try {
        $crawler = new MellemfeedCrawler($mellemfeedUrl);
        $crawler->crawl();
        $this->mellemfeedLinks[$mellemfeedUrl] = $crawler->getLinkGroups();
      } catch (\Exception $e) {
        $this->logger->error("Could not get mellemfeed links for group: ${group}. Error message: " . $e->getMessage());
        $this->mellemfeedLinks[$mellemfeedUrl] = [];
      }
    }
    return empty($this->mellemfeedLinks[$mellemfeedUrl][$group]) ? [] : $this->mellemfeedLinks[$mellemfeedUrl][$group];
  }

}
