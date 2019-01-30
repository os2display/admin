<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Cron;


use Kkos2\KkOs2DisplayIntegrationBundle\Slides\Events\MockEventsData;
use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventsSisCron implements EventSubscriberInterface
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
   * @var int $numberOfEvents
   */
  private $numberOfEvents;

  public function __construct($container)
  {
    $this->container = $container;
    $this->logger = $this->container->get('logger');
  }

  public static function getSubscribedEvents()
  {
    return [
      'os2displayslidetools.sis_cron.kk_events_sis_cron' => [
        ['getSlideData'],
      ]
    ];
  }

  public function getSlideData(SlidesInSlideEvent $event)
  {
    $slide = $event->getSlidesInSlide();
    $this->numberOfEvents = $slide->getOption('sis_total_items', 12);

    $mockData = new MockEventsData($this->numberOfEvents);
    $slide->setSubslides($mockData->getEvents());
  }
}
