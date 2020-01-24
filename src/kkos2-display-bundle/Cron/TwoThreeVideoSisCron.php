<?php


namespace Kkos2\KkOs2DisplayIntegrationBundle\Cron;


use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\DataFetcher;
use Kkos2\KkOs2DisplayIntegrationBundle\Slides\DateTrait;
use Psr\Log\LoggerInterface;
use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class TwoThreeVideoSisCron implements EventSubscriberInterface {

  use DateTrait;

  /**
   * @var \Psr\Log\LoggerInterface $logger
   */
  private $logger;

  /**
   * @var \Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\DataFetcher
   */
  private $fetcher;


  /**
   * TwoThreeVideo constructor.
   *
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(LoggerInterface $logger,DataFetcher $fetcher) {
    $this->logger = $logger;
    $this->fetcher = $fetcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'os2displayslidetools.sis_cron.twothreevideo_sis_cron' => [
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
    $twoThreeOptions = $slide->getOption('twothreevideo', []);

    $url = 'https://video.kk.dk/api/photo/list';
    $params = [
      'format' => 'json',
      'varname' => '',
      'album_id' => $twoThreeOptions['album_id'],
      'size' => $slide->getOption('sis_total_items', 12),
    ];
    $url .= '?' . http_build_query($params);
    $videos = [];
    try {
      $responseBody = $this->fetcher->getBody($url, ['connect_timeout' => 5]);
      $data = json_decode($responseBody, true);
      if (!empty($data['photos'])) {
        $videos = array_map([$this, 'processVideos'], $data['photos']);
      }

    } catch (\Exception $e) {
      $this->logger->error($e->getMessage());
    }

    $slide->setSubslides($videos);
  }

  private function processVideos($item) {

    $params = [
      'autoPlay' => 0,
      'loop' => 0,
      'showDescriptions' => 0,
      'showLogo' => 0,
      'socialSharing' => 0,
      'hideBigPlay' => 1,
      'showTray' => 0,
      'defaultQuality' => 'fullhd',
      'autoMute' => 1,
      'source' => 'embed',
      'photo_id' => $item['photo_id'],
    ];
    $video = [
      'title' => $item['one'],
      'video_length' => $item['video_length'],
      'url' => 'https://video.kk.dk/v.ihtml/player.html?' . http_build_query($params),
    ];
    return $video;
  }

}
