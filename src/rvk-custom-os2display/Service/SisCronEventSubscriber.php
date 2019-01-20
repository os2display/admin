<?php

namespace Reykjavikurborg\RvkCustomOs2Display\Service;

use Reload\Os2DisplaySlideTools\Events\SlidesInSlideEvent;
use Reload\Os2DisplaySlideTools\Slides\DataFetcher;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SisCronEventSubscriber implements EventSubscriberInterface {

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
      'os2displayslidetools.sis_cron.rvk_custom_sis_cron' => [
        ['getSlideData'],
      ]
    ];
  }

  public function getSlideData(SlidesInSlideEvent $event)
  {
    $slide = $event->getSlidesInSlide();
    $this->dataUrl = $slide->getOption('bib_event_datasource');

    $this->numberOfEvents = $slide->getOption('sis_total_items', 12);

    $slide->setSubslides($this->getEvents());
  }



  private function getEvents()
  {
    $events = [];
    $rss = DataFetcher::fetchSimpleXml($this->dataUrl);
    foreach ($rss->channel->item as $item) {
      $event = $this->parseDescription((string) $item->description);
      $event['title'] = (string) $item->title;
      $events[] = $event;

      if (count($events) >= $this->numberOfEvents) {
        break;
      }
    }
    return array_reverse($events);
  }

  private function parseDescription($html)
  {
    // Wrap HTML to make absolutely sure the parser knows it's UTF-8.
    $wrapped = <<<HTML
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
$html
</body>
</html>
HTML;

    $crawler = new Crawler($wrapped);

    $data = [];
    $img = $crawler->filter('.field-name-field-ding-event-list-image img');
    $data['image'] = $img->count() ? $img->eq(0)->attr('src') : '';

    $data['description'] = $crawler->filter('.field-name-field-ding-event-lead .field-item')->eq(0)->html();
    $data['date'] = $crawler->filter('.field-name-field-ding-event-date .field-item')->eq(0)->html();
    $data['categories'] = $crawler->filter('.field-name-field-ding-event-category .field-item')->each(function (Crawler $a, $i) {
      return $a->html();
    });

    $library = $crawler->filter('.field-name-og-group-ref .field-item');
    $data['location'] = $library->count() ? $library->eq(0)->html() : '';

    $address = $crawler->filter('.addressfield-container-inline .name-block');
    if ($address->count()) {
      if (!empty($data['location'])) {
        $data['location'] .= ', ';
      }
      $data['location'] .= $address->eq(0)->html();
    }

    return $data;
  }

}