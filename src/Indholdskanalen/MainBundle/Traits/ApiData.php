<?php

namespace Indholdskanalen\MainBundle\Traits;

use JMS\Serializer\Annotation as Serializer;

trait ApiData {
  /**
   * @var mixed
   * @Serializer\Groups("api")
   */
  protected $apiData;

  /**
   * @param mixed $apiData
   *
   * @return ApiData
   */
  public function setApiData($apiData) {
    $this->apiData = $apiData;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getApiData() {
    return $this->apiData;
  }
}
