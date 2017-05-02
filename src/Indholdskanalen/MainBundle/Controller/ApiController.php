<?php

namespace Indholdskanalen\MainBundle\Controller;

use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller {
  /**
   * Deserialize JSON content from request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return array
   */
  protected function getData(Request $request) {
    $data = $request->getContent();
    $serializer = $this->get('serializer');

    return (array)$serializer->deserialize($data, 'array', 'json');
  }

  /**
   * Create a JSON response with serialized data.
   *
   * @param $data
   * @param int $status
   * @param array $headers
   * @param array $serializationGroups
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  protected function json($data, $status = 200, array $headers = [], array $serializationGroups = ['api']) {
    $response = new JsonResponse(null, $status, $headers);

    $serializer = $this->get('serializer');
    $context = SerializationContext::create()->enableMaxDepthChecks();
    if ($serializationGroups) {
      $context->setGroups($serializationGroups);
    }
    $content = $serializer->serialize($data, 'json', $context);
    $response->setContent($content);

    return $response;
  }

  /**
   * Apply values to an object.
   *
   * @param $entity
   * @param array $data
   */
  protected function setValues($entity, array $data) {
    $entityService = $this->get('os2display.entity_service');
    $entityService->setValues($entity, $data);
  }

  protected function validateEntity($entity) {
    $entityService = $this->get('os2display.entity_service');

    return $entityService->validateEntity($entity);
  }

  /**
   * Convenience method.
   *
   * @param $entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  protected function setValuesFromRequest($entity, Request $request) {
    $data = $this->getData($request);
    $this->setValues($entity, $data);
  }
}
