<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides;

use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\JsonFetcher;
use Psr\Log\LoggerInterface;

class EventFeedData extends EventData
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

  public function getEvents()
  {
    try {
      $fetched = JsonFetcher::fetch($this->dataUrl);
      $data = array_slice($fetched, 0, $this->numItems);
      $events = array_map([$this, 'extractData'], $data);
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
