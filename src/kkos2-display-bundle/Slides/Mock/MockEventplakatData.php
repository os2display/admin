<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides\Mock;


class MockEventplakatData
{
  private $numItems;

  public function __construct($numItems)
  {
    $this->numItems = $numItems;
  }

  public function getEventPlakater()
  {
    // TODO. Respect $numItems
    $json = file_get_contents(__DIR__ . '/../../mockdata/events.json');
    $data = json_decode($json, true);
    return $data;
  }

}
