<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides;


use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\JsonFetcher;
use Psr\Log\LoggerInterface;

class PlakatEventFeedData extends EventData
{
  /**
   * @var \Psr\Log\LoggerInterface $logger
   */
  protected $logger;

  private $numItems;

  private $dataUrl;

  public function __construct(LoggerInterface $logger, $dataUrl, $numItems)
  {
    $this->logger = $logger;
    $this->dataUrl = $dataUrl;
    $this->numItems = $numItems;
  }

  public function getPlakatEvents()
  {
    try {
      $json = JsonFetcher::fetch($this->dataUrl);
      $events = array_map([$this, 'extractData'], $json);
    } catch (\Exception $e) {
      $this->logger->error($e->getMessage());
      return [];
    }
    if ($this->hasMissing()) {
      $this->logger->warning(
        'Missing fields while processing ' . $this->dataUrl
      );
      $this->logStatus($this->logger);
    }

    return $events;
  }
}
