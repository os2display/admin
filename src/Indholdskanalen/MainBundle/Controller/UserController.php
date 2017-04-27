<?php
/**
 * @file
 * Contains user controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\User;
use Indholdskanalen\MainBundle\Entity\UserGroup;
use Indholdskanalen\MainBundle\Exception\DuplicateEntityException;
use Indholdskanalen\MainBundle\Exception\ValidationException;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/api/user")
 */
class UserController extends ApiController {
  /**
   * Lists all user entities.
   *
   * @Route("", name="api_user_index")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();
    $users = $em->getRepository(User::class)->findAll();

    return $this->json($users);
  }

  /**
   * Creates a new user entity.
   *
   * @Route("", name="api_user_new")
   * @Method({"POST"})
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function newAction(Request $request) {
    // Get post content.
    $data = $this->getData($request);

    // Create user.
    try {
      $user = $this->get('os2display.user_manager')->createUser($data);
    }
    catch (ValidationException $e) {
      return $this->json($e, 400);
    }
    catch (DuplicateEntityException $e) {
      return $this->json($e, 409);
    }

    // Send response.
    return $this->json($user, 201);
  }

  /**
   * Sends current user.
   *
   * @Route("/current", name="api_user_current")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getCurrentUser() {
    $user = $this->get('security.token_storage')->getToken()->getUser();

    // Hack to include configurable search_filter_default
    // @TODO: move this into the user and make it configurable on a user level.
		$user->search_filter_default = $this->getParameter('search_filter_default');

    return $this->json($user);
  }

  /**
   * Finds and displays a user entity.
   *
   * @Route("/{id}", name="api_user_show")
   * @Method("GET")
   *
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function showAction(User $user) {
    return $this->json($user);
  }

  /**
   * Displays a form to edit an existing user entity.
   *
   * @Route("/{id}", name="api_user_edit")
   * @Method({"PUT"})
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function editAction(Request $request, User $user) {
    $this->setValuesFromRequest($user, $request);

    // Validate entity.
    $errors = $this->validateEntity($user);
    if (count($errors) > 0) {
      // Send error response.
      return $this->json($errors, 400);
    }

    // Persist to database.
    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();

    // Send response.
    return $this->json($user);
  }

  /**
   * Deletes a user entity.
   *
   * @Route("/{id}", name="api_user_delete")
   * @Method("DELETE")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function deleteAction(Request $request, User $user) {
    $em = $this->getDoctrine()->getManager();
    $em->remove($user);
    $em->flush();

    return new Response(NULL, 204);
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
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function addGroup(Request $request, User $user, Group $group) {
    $em = $this->getDoctrine()->getManager();

    // Get post content.
    $data = $this->getData($request);

    $role = isset($data->role) ? $data->role : null;

    // Check if group is already added.
    $userGroup = $em->getRepository('IndholdskanalenMainBundle:UserGroup')->findBy(['user' => $user->getId(), 'group' => $group->getId(), 'role' => $role]);
    if (!empty($userGroup)) {
      return $this->json([
        'message' => 'Group already added',
      ], 409);
    }

    $userGroup = new UserGroup();
    $userGroup->setUser($user);
    $userGroup->setGroup($group);
    $userGroup->setRole($role);
    $em->persist($userGroup);
    $em->flush();

    // Send response.
    return $this->json($userGroup, 201);
  }
}
