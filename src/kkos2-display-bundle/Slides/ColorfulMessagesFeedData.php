<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides;


use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\JsonFetcher;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ColorfulMessagesFeedData
{

  /**
   * @var \Symfony\Bridge\Monolog\Logger $logger
   */
  private $logger;

  private $numItems;

  private $dataUrl;

  public function __construct(ContainerInterface $container, $dataUrl, $numItems)
  {
    $this->container = $container;
    $this->logger = $this->container->get('logger');
    $this->dataUrl = $dataUrl;
    $this->numItems = $numItems;
  }


  public function getColorfulMessages()
  {
    $json = JsonFetcher::fetch($this->dataUrl);
    return  array_map([$this, 'extractData'], $json);
  }

  private function extractData(array $data) {
    return [
      'place' => trim($data['field_display_institution_spot']),
      'title' => trim($data['title_field']),
      'body' => trim($data['body']),
      'background_color' => trim($data['field_background_color'])
    ];
  }

}
