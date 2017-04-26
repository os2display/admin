<?php
/**
 * @file
 * Contains custom json response.
 */

namespace Indholdskanalen\MainBundle;

use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;

/**
 * Class CustomJsonResponse.
 *
 * @package Indholdskanalen\MainBundle
 */
class CustomJsonResponse extends Response {

  /**
   * CustomJsonResponse constructor.
   */
  public function __construct($statusCode = 200, $headers = []) {
    parent::__construct('', $statusCode, $headers);

    $this->headers->set('Content-Type', 'application/json');
  }

  /**
   * Set json data.
   *
   * @param $data
   */
  public function setJsonData($data) {
    $this->setContent($data);
  }

  /**
   * Set data as array, with a serialization context.
   *
   * @param $data
   * @param $serializer
   * @param null $serializationGroups
   */
  public function setData($data, $serializer, $serializationGroups = NULL) {
    if (isset($serializationGroups)) {
      $data = $serializer->serialize($data, 'json', SerializationContext::create()
        ->setGroups($serializationGroups)
        ->enableMaxDepthChecks());
    }
    else {
      $data = $serializer->serialize($data, 'json', SerializationContext::create()
        ->enableMaxDepthChecks());
    }

    $this->setContent($data);
  }
}
