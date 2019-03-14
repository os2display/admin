<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Cron;


use Kkos2\KkOs2DisplayIntegrationBundle\Slides\Mock\MockEventplakatData;
use Kkos2\KkOs2DisplayIntegrationBundle\Slides\PlakatEventFeedData;
use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventplakatSisCron implements EventSubscriberInterface {

  /**
   * @var \Symfony\Bridge\Monolog\Logger $logger
   */
  private $logger;
  /**
   * @var \Symfony\Component\DependencyInjection\ContainerInterface $container
   */
  private $container;

   public function __construct($container)
  {
    $this->container = $container;
    $this->logger = $this->container->get('logger');
  }

  public static function getSubscribedEvents()
  {
    return [
      'os2displayslidetools.sis_cron.kk_eventplakat_sis_cron' => [
        ['getSlideData'],
      ]
    ];
  }

  public function getSlideData(SlidesInSlideEvent $event)
  {
    $slide = $event->getSlidesInSlide();

    // Make sure that only one subslide pr. slide is set. The value is
    // for the user, but the plakat slides don't support more than one, so
    // enforce it here.
    $slide->setOption('sis_items_pr_slide', 1);
    $numItems = $slide->getOption('sis_total_items', 12);
    $url = $slide->getOption('datafeed_url', '');

    $fetcher = new PlakatEventFeedData($this->container, $url, $numItems);
    $events = $fetcher->getPlakatEvents();
    $slide->setSubslides($events);
  }

  public function getMockSlideData(SlidesInSlideEvent $event)
  {
    $slide = $event->getSlidesInSlide();
    // Make sure that only one subslide pr. slide is set. The value is
    // for the user, but the colorful slides don't support more than one, so
    // enforce it here.
    $slide->setOption('sis_items_pr_slide', 1);
    $numItems = $slide->getOption('sis_total_items', 12);
    $mockData = new MockEventplakatData($numItems);
    $slide->setSubslides($mockData->getEventPlakater());
  }
}
