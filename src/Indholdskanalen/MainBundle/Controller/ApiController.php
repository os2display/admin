<?php

namespace Indholdskanalen\MainBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Indholdskanalen\MainBundle\Entity\Channel;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\Screen;
use Indholdskanalen\MainBundle\Entity\Slide;
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
    $securityMananger = $this->get('os2display.security_manager');

    $group->setApiData([
      'permissions' => [
        'can_read' => $securityMananger->decide(EditVoter::READ, $group),
        'can_update' => $securityMananger->decide(EditVoter::UPDATE, $group),
        'can_delete' => $securityMananger->decide(EditVoter::DELETE, $group),

        'can_add_user' => $securityMananger->decide('can_add_user', $group),
        'can_add_channel' => $securityMananger->decide('can_add_channel', $group),
        'can_add_slide' => $securityMananger->decide('can_add_slide', $group),
        'can_add_screen' => $securityMananger->decide('can_add_screen', $group),
      ]
    ]);
  }

  protected function setApiDataUser(User $user) {
    $securityMananger = $this->get('os2display.security_manager');

    $permissions = [
      'can_read' => $securityMananger->decide(EditVoter::READ, $user),
      'can_update' => $securityMananger->decide(EditVoter::UPDATE, $user),
      'can_delete' => $securityMananger->decide(EditVoter::DELETE, $user),
    ];

    // Add permissions for current user.
    $token = $this->get('security.token_storage')->getToken();
    if ($token && $user == $token->getUser()) {
      $permissions += [
        'can_create_group' => $securityMananger->decide(EditVoter::CREATE, Group::class),
        'can_create_user' => $securityMananger->decide(EditVoter::CREATE, User::class),
        'can_create_channel' => $securityMananger->decide([EditVoter::CREATE], Channel::class),
        'can_create_slide' => $securityMananger->decide([EditVoter::CREATE], Slide::class),
        'can_create_screen' => $securityMananger->decide([EditVoter::CREATE], Screen::class),
      ];
    }

    $user->setApiData(['permissions' => $permissions]);

    $translator = $this->get('translator');
    $request = $this->container->get('request_stack')->getCurrentRequest();
    $locale = $request->get('locale', $this->getParameter('locale'));

    $roleNames = [];
    foreach ($user->getRoles(FALSE, FALSE) as $roleName) {
      $roleNames[$roleName] = $translator->trans($roleName, [], 'IndholdskanalenMainBundle', $locale);
    }

    $user->setRoleNames($roleNames);
  }

}
