<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides;


use Kkos2\KkOs2DisplayIntegrationBundle\ExternalData\JsonFetcher;
use Psr\Log\LoggerInterface;

class ColorfulMessagesFeedData
{

  /**
   * List of fields that where missing when importing data.
   * @var array missing
   */
  protected $missing = [];

  /**
   * @var \Psr\Log\LoggerInterface $logger
   */
  private $logger;

  private $numItems;

  private $dataUrl;

  public function __construct(LoggerInterface $logger, $dataUrl, $numItems)
  {
    $this->logger = $logger;
    $this->dataUrl = $dataUrl;
    $this->numItems = $numItems;
  }

  public function getColorfulMessages()
  {
    try {
      $json = JsonFetcher::fetch($this->dataUrl);
      $data = array_map([$this, 'extractData'], $json);
    } catch (\Exception $e) {
      $this->logger->error($e->getMessage());
      return [];
    }
    if (count($this->missing) > 0) {
      $this->logger->warning(
        'Missing fields while processing ' . $this->dataUrl
      );
      foreach ($this->missing as $missing) {
        $this->logger->warning('Missing ' . implode(', ', $missing));
      }
    }
    return $data;
  }

  private function extractData(array $data) {
    $expected_keys = ['field_display_institution_spot', 'title_field', 'body', 'field_background_color'];
    $missing = array_diff($expected_keys, array_keys($data));
    if (count($missing) > 0){
      $this->missing[] = $missing;
      return [];
    }

    return [
      'place' => trim($data['field_display_institution_spot']),
      'title' => trim($data['title_field']),
      'body' => trim($data['body']),
      'background_color' => trim($data['field_background_color'])
    ];
  }

}
