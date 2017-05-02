<?php

namespace Indholdskanalen\MainBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends FOSRestController {
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
   * @param $data
   * @param array $headers
   * @param array $serializationGroups
   * @return \Symfony\Component\HttpFoundation\Response
   */
  protected function createCreatedResponse($data, array $headers = [], array $serializationGroups = ['api']) {
    $view = $this->view($data, Codes::HTTP_CREATED);
    $context = $view->getSerializationContext();
    $context->setGroups($serializationGroups);
    
    return $this->handleView($view);
  }

  /**
   * Apply values to an object.
   *
   * @param $entity
   * @param array $data
   */
  protected function setValues($entity, array $data, array $properties = NULL) {
    $entityService = $this->get('os2display.entity_service');
    $entityService->setValues($entity, $data, $properties);
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
  protected function setValuesFromRequest($entity, Request $request, array $properties = NULL) {
    $data = $this->getData($request);
    $this->setValues($entity, $data, $properties);
  }
}
