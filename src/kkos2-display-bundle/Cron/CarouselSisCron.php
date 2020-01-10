<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Cron;

use GuzzleHttp\Exception\GuzzleException;
use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\DataFetcher;
use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\MultisiteCrawler;
use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\MultisiteHelper;
use Psr\Log\LoggerInterface;
use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CarouselSisCron
 *
 * @package Kkos2\KkOs2DisplayIntegrationBundle\Cron
 */
class CarouselSisCron implements EventSubscriberInterface {

  /**
   * @var \Symfony\Bridge\Monolog\Logger $logger
   */
  private $logger;

  /**
   * @var \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\DataFetcher
   */
  private $fetcher;

  /**
   * @var \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\MultisiteCrawler
   */
  private $crawler;

  /**
   * @var \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\MultisiteHelper
   */
  private $multiHelp;


  /**
   * CarouselSisCron constructor.
   *
   * @param \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\DataFetcher $fetcher
   * @param \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\MultisiteCrawler $crawler
   * @param \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\MultisiteHelper $multiHelp
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(DataFetcher $fetcher, MultisiteCrawler $crawler, MultisiteHelper $multiHelp, LoggerInterface $logger) {
    $this->fetcher = $fetcher;
    $this->crawler = $crawler;
    $this->multiHelp = $multiHelp;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'os2displayslidetools.sis_cron.kk_carousel_sis_cron' => [
        ['getSlideData'],
      ],
    ];
  }

  /**
   * Get data for event.
   *
   * @param \Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent $event
   */
  public function getSlideData(SlidesInSlideEvent $event) {
    $slide = $event->getSlidesInSlide();
    $url = $slide->getOption('datafeed_url', '');

    try {
      $html = $this->fetcher->getBody($url);

      $imageUrls = $this->crawler->getImageUrls($html, '.node-image-slideshow .slides li img');

      $images = array_map(function ($url) {
        return [
          'image_url' => $this->multiHelp->getOriginalImagePath($url),
        ];
      },
        $imageUrls);
      $slide->setSubslides($images);
    } catch (GuzzleException $e) {
      $slide->setSubslides([]);
      $this->logger->addError("There was a problem fetching data from $url");
    }
  }

}
