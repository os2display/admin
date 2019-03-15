<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides\Mock;

class MockEventsData
{
  private $numItems;

  public function __construct($numItems)
  {
    $this->numItems = $numItems;
  }

  public function getEvents()
  {
    // TODO. Respect $numItems
    $json = file_get_contents(__DIR__ . '/../../mockdata/events.json');
    $data = json_decode($json, true);
    return $data;
  }
}
