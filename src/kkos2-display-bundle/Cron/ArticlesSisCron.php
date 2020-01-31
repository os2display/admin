<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Cron;

use Kkos2\KkOs2DisplayIntegrationBundle\Slides\DateTrait;
use Psr\Log\LoggerInterface;
use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BookByenSisCron
 *
 * @package Kkos2\KkOs2DisplayIntegrationBundle\Cron
 */
class ArticlesSisCron implements EventSubscriberInterface {

  use DateTrait;

  /**
   * @var \Symfony\Bridge\Monolog\Logger $logger
   */
  private $logger;

  /**
   * BookByenSisCron constructor.
   *
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'os2displayslidetools.sis_cron.kk_articles_sis_cron' => [
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
    $fakeData = [
      [
        'title' => 'Cat Lorem Ipsum',
        'manchet' => 'Refuse to come home when humans are going to bed',
        'image' => 'https://github.com/kkos2/os2display-admin/raw/kk-master/src/kkos2-display-bundle/Resources/public/test/testimage.png',
      ],
      [
        'title' => 'Kitty Lorem eating fishes',
        'manchet' => 'Taco cat backwards spells taco cat dream about hunting birds',
        'image' => 'https://github.com/kkos2/os2display-admin/raw/kk-master/src/kkos2-display-bundle/Resources/public/test/testimage.png',
        'freetext' => 'loves cheeseburgers',
      ],
      [
        'title' => 'Lorem Tuna Kitteh Ipsum',
        'manchet' => 'Plop down in the middle where everybody walks cat mojo',
        'image' => 'https://github.com/kkos2/os2display-admin/raw/kk-master/src/kkos2-display-bundle/Resources/public/test/testimage.png',
        'freetext' => 'Let me-out, let me-aow, let meaow, meaow',
      ],
    ];

    $slide->setSubslides($fakeData);
  }


}
