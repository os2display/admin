<?php
/**
 * @file
 * Contains user controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use Indholdskanalen\MainBundle\CustomJsonResponse;
use Indholdskanalen\MainBundle\Entity\User;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\UserGroup;

/**
 * @Route("/api/user")
 */
class UserController extends Controller {
  /**
   * Lists all user entities.
   *
   * @Route("", name="api_user_index")
   * @Method("GET")
   *
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $users = $em->getRepository('IndholdskanalenMainBundle:User')->findAll();

    $response = new CustomJsonResponse();
    $response->setData($users, $this->get('jms_serializer'), ['api']);
    return $response;
  }

  /**
   * Creates a new user entity.
   *
   * @Route("", name="api_user_new")
   * @Method({"POST"})
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function newAction(Request $request) {
    // Set up new User.
    $user = new User();

    // Get the Entity Service.
    $entityService = $this->get('os2display.entity_service');

    // Get post content.
    $post = json_decode($request->getContent());

    // Set values from request.
    $entityService->setValues($user, $post);

    // Validate entity.
    $errors = $entityService->validateEntity($user);
    if (count($errors) > 0) {
      // Send error response.
      $response = new CustomJsonResponse(400);
      $response->setData($errors, $this->get('jms_serializer'));
      return $response;
    }

    // Persist to database.
    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();

    // Send response.
    $response = new CustomJsonResponse(201);
    $response->setJsonData(json_encode(['id' => $user->getId()]));
    return $response;
  }

  /**
   * Finds and displays a user entity.
   *
   * @Route("/{id}", name="api_user_show")
   * @Method("GET")
   *
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function showAction(User $user) {
    $response = new CustomJsonResponse();
    $response->setData($user, $this->get('jms_serializer'), ['api']);
    return $response;
  }

  /**
   * Displays a form to edit an existing user entity.
   *
   * @Route("/{id}", name="api_user_edit")
   * @Method({"PUT"})
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function editAction(Request $request, User $user) {
    // Get the Entity Service.
    $entityService = $this->get('os2display.entity_service');

    // Get post content.
    $post = json_decode($request->getContent());

    // Set values from request.
    $entityService->setValues($user, $post);

    // Validate entity.
    $errors = $entityService->validateEntity($user);
    if (count($errors) > 0) {
      // Send error response.
      $response = new CustomJsonResponse(400);
      $response->setData($errors, $this->get('jms_serializer'));
      return $response;
    }

    // Persist to database.
    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();

    // Send response.
    $response = new CustomJsonResponse();
    $response->setData($user, $this->get('jms_serializer'), ['api']);
    return $response;
  }

  /**
   * Deletes a user entity.
   *
   * @Route("/{id}", name="api_user_delete")
   * @Method("DELETE")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function deleteAction(Request $request, User $user) {
    $em = $this->getDoctrine()->getManager();
    $em->remove($user);
    $em->flush();

    return new CustomJsonResponse(204);
  }

  /**
   * Displays a form to edit an existing user entity.
   *
   * @Route("/{user}/group/{group}", name="api_user_add_group")
   * @Method({"POST"})
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function addGroup(Request $request, User $user, Group $group) {
    $em = $this->getDoctrine()->getManager();

    // Get post content.
    $post = json_decode($request->getContent());

    $role = isset($post->role) ? $post->role : null;

    // Check if group is already added.
    $userGroup = $em->getRepository('IndholdskanalenMainBundle:UserGroup')->findBy(['user' => $user->getId(), 'group' => $group->getId(), 'role' => $role]);
    if (!empty($userGroup)) {
      $response = new CustomJsonResponse(409);
      $response->setJsonData(json_encode(['message' => 'Group already added']));
      return $response;
    }

    $userGroup = new UserGroup();
    $userGroup->setUser($user);
    $userGroup->setGroup($group);
    $userGroup->setRole($role);
    $em->persist($userGroup);
    $em->flush();

    // Send response.
    $response = new CustomJsonResponse();
    $response->setJsonData(json_encode(['id' => $userGroup->getId()]));
    return $response;
  }

  /**
   * Sends current user.
   *
   * @Route("/current")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getCurrentUser() {
    $user = $this->get('security.context')->getToken()->getUser();

    $serializer = $this->get('jms_serializer');

    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    $json_content = $serializer->serialize($user, 'json', SerializationContext::create()->setUsers(array('api')));

    // Hack to include configurable search_filter_default
    // @TODO: move this into the user and make it configurable on a user level.
    $user = json_decode($json_content);
    $user->search_filter_default = $this->getParameter('search_filter_default');
    $json_content = json_encode($user);

    $response->setContent($json_content);

    return $response;
  }
}
