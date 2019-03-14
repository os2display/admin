<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Slides\Mock;


class MockColorfulMessagesData
{
  private $numItems;

  public function __construct($numItems)
  {
    $this->numItems = $numItems;
  }

  public function getColorfulMessages()
  {
    // TODO. Respect $numItems
    $json = file_get_contents(__DIR__ . '/../../mockdata/colorful-messages.json');
    $data = json_decode($json, true);
    return $data;
  }

}
