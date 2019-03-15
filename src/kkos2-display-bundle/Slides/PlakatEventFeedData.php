<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides;


use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\JsonFetcher;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PlakatEventFeedData extends EventData
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

  public function getPlakatEvents()
  {
    $json = JsonFetcher::fetch($this->dataUrl);
    return  array_map([$this, 'extractData'], $json);
  }

}
