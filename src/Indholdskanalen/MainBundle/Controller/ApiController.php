<?php

namespace Indholdskanalen\MainBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\User;
use Indholdskanalen\MainBundle\Security\EditVoter;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Role\Role;

class ApiController extends FOSRestController {

  protected function findAll($class) {
    return $this->findBy($class, []);
  }

  protected function findBy($class, array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL) {
    $manager = $this->get('os2display.entity_manager');
    return $manager->findBy($class, $criteria, $orderBy, $limit, $offset);
  }

  /**
   * Deserialize JSON content from request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return array
   */
  protected function getData(Request $request, $key = NULL) {
    return $key ? $request->request->get($key) : $request->request->all();
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
    $response = new JsonResponse(NULL, $status, $headers);

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

  /**
   * Set API data on an object or a list of objects.
   *
   * @param $object
   * @return mixed
   */
  protected function setApiData($object) {
    if (is_array($object)) {
      foreach ($object as $item) {
        $this->setApiData($item);
      }
    }
    elseif ($object instanceof Group) {
      $this->setApiDataGroup($object);
    }
    elseif ($object instanceof User) {
      $this->setApiDataUser($object);
    }

    return $object;
  }

  protected function setApiDataGroup(Group $group) {
    $token = $this->get('security.token_storage')->getToken();
    $decisionManager = $this->get('security.access.decision_manager');

    $group->setApiData([
      'permissions' => [
        'can_read' => $decisionManager->decide($token, [EditVoter::READ], $group),
        'can_update' => $decisionManager->decide($token, [EditVoter::UPDATE], $group),
        'can_delete' => $decisionManager->decide($token, [EditVoter::DELETE], $group),
      ]
    ]);
  }

  protected function setApiDataUser(User $user) {
    $token = $this->get('security.token_storage')->getToken();
    $decisionManager = $this->get('security.access.decision_manager');

    $user->setApiData([
      'permissions' => [
        'can_read' => $decisionManager->decide($token, [EditVoter::READ], $user),
        'can_update' => $decisionManager->decide($token, [EditVoter::UPDATE], $user),
        'can_delete' => $decisionManager->decide($token, [EditVoter::DELETE], $user),
        'can_create_group' => $decisionManager->decide($token, [EditVoter::CREATE], Group::class),
        'can_create_user' => $decisionManager->decide($token, [EditVoter::CREATE], User::class),
      ]
    ]);

    $userRoles = array_map(function ($role) {
      return new Role($role);
    }, $user->getRoles(FALSE));
    $roles = $this->get('security.role_hierarchy')->getReachableRoles($userRoles);
    $userRoles = array_map(function (Role $role) {
      return $role->getRole();
    }, $roles);

    $user->setUserRoles(array_unique($userRoles));
  }

}
