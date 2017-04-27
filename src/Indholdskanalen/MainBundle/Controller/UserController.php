<?php
/**
 * @file
 * Contains user controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\CustomJsonResponse;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\User;
use Indholdskanalen\MainBundle\Entity\UserGroup;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
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
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function newAction(Request $request) {
    // Get post content.
    $post = json_decode($request->getContent());

    if (!isset($post)) {
      return new CustomJsonResponse(400);
    }

    $userManager = $this->container->get('fos_user.user_manager');
    $user = $userManager->findUserByEmail($post->email);
    if ($user) {
      return new CustomJsonResponse(409);
    }

    // Create user object.
    $user = $userManager->createUser();
    $user->setUsername($post->email);
    $user->setEmail($post->email);
    $user->setPlainPassword(uniqid());
    $user->setFirstname($post->firstname);
    $user->setLastname($post->lastname);
    $user->setEnabled(TRUE);

    // Send confirmation email.
    if (null === $user->getConfirmationToken()) {
      /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
      $tokenGenerator = $this->container->get('fos_user.util.token_generator');
      $user->setConfirmationToken($tokenGenerator->generateToken());
    }
    $this->container->get('os2display.user_mailer_service')->sendUserCreatedEmailMessage($user);
    $user->setPasswordRequestedAt(new \DateTime());

    $userManager->updateUser($user);

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
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
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
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
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
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
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
