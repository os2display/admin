<?php
/**
 * @file
 * Contains the DING2 service.
 *
 * Provides integration service with DING2.
 */

namespace Kkos2\KkOs2DisplayIntegrationBundle\Service;


use FOS\RestBundle\Decoder\XmlDecoder;
use GuzzleHttp\Client;
use Os2Display\CoreBundle\Entity\Slide;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Os2Display\CoreBundle\Events\CronEvent;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class Kkos2DisplayService
{

  /**
   * @var \Symfony\Bridge\Monolog\Logger $logger
   */
//  private $logger;

  private $container;

  /**
   * TODO
   */
  public function __construct($container)
  {
    $this->container = $container;
//    $this->logger = $this->container->get('logger');
    error_log(get_class($this->container));
  }

  /**
   * ik.onCron event listener.
   *
   * Updates calendar slides.
   *
   * @param CronEvent $event
   */
  public function onCron(CronEvent $event)
  {

    $oldLocale = setlocale(LC_TIME, 0);
    setlocale(LC_TIME, "da_DK.utf8");

    // TODO. Not sure about the date stuff?

    $this->updateEvents();
    setlocale(LC_TIME, $oldLocale);
  }

  private function updateEvents()
  {
    $slideRepo = $this->container->get('doctrine')->getRepository('Os2DisplayCoreBundle:Slide');
    /** @var \Os2Display\CoreBundle\Entity\Slide[] $slides */
    $slides = $slideRepo->findBySlideType('kultunaut-event');

    $normalizer = new GetSetMethodNormalizer();
    $encoder = new XmlEncoder();
    $serializer = new Serializer(array($normalizer), array($encoder));

    $client = new Client();


    foreach ($slides as $slide) {
      $options = $slide->getOptions();
      if (!empty($options['source'])) {
        $response = $client->get($options['source'], [
          'headers' => [
            'Accept' => 'application/xml'
          ]
        ]);
        $body = $response->getBody();
        // TODO. Error handling. Og genbrug af XML hvis det er samme feed.
        $contents = (string) $body;
        $xml = simplexml_load_string($contents);

        foreach($xml->item as $item) {
          /** @var \Kkos2\KkOs2DisplayIntegrationBundle\Events\Event */
        $event = $serializer->deserialize($item->asXml(), 'Kkos2\KkOs2DisplayIntegrationBundle\Events\Event', 'xml');
        }
      }
    }
  }
}
