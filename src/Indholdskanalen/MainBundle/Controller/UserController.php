<?php
/**
 * @file
 * Contains user controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\User;
use Indholdskanalen\MainBundle\Entity\UserGroup;
use Indholdskanalen\MainBundle\Exception\DuplicateEntityException;
use Indholdskanalen\MainBundle\Exception\HttpDataException;
use Indholdskanalen\MainBundle\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/user")
 * @Rest\View(serializerGroups={"api"})
 */
class UserController extends ApiController {
  protected static $editableProperties = ['email', 'firstname', 'lastname'];

  /**
   * Lists all user entities.
   *
   * @Rest\Get("", name="api_user_index")
   * @Rest\QueryParam(name="filter", array=true, nullable=true, description="Filter.")
   *
   * @param \FOS\RestBundle\Request\ParamFetcherInterface $paramFetcher
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function indexAction(ParamFetcherInterface $paramFetcher) {
    $em = $this->getDoctrine()->getManager();
// $filter = $paramFetcher->get('filter');
// $users = $em->getRepository(User::class)->findBy($filter);
    $users = $em->getRepository(User::class)->findAll();

    return $users;
  }

  /**
   * Creates a new user entity.
   *
   * @Rest\Post("", name="api_user_new")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return User
   */
  public function newAction(Request $request) {
    // Get post content.
    $data = $this->getData($request);

    // Create user.
    try {
      $user = $this->get('os2display.user_manager')->createUser($data);
    }
    catch (ValidationException $e) {
      throw new HttpDataException(Codes::HTTP_BAD_REQUEST, $data, 'Invalid data', $e);
    }
    catch (DuplicateEntityException $e) {
      throw new HttpDataException(Codes::HTTP_CONFLICT, $data, 'Duplicate user', $e);
    }

    // Send response.
    return $this->createCreatedResponse($user);
  }

  /**
   * Sends current user.
   *
   * @Rest\Get("/current", name="api_user_current")
   *
   * @return User
   */
  public function getCurrentUser() {
    $user = $this->get('security.token_storage')->getToken()->getUser();

    if (!$user) {
      throw $this->createNotFoundException('No current user');
    }

    return $this->showAction($user);
  }

  /**
   * Finds and displays a user entity.
   *
   * @Rest\Get("/{id}", name="api_user_show")
   *
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @return User
   */
  public function showAction(User $user) {
    return $user;
  }

  /**
   * @Rest\Put("/{id}", name="api_user_edit")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @return User
   */
  public function editAction(Request $request, User $user) {
    $this->setValuesFromRequest($user, $request, static::$editableProperties);

    try {
      $this->validateEntity($user);
    } catch (ValidationException $e) {
      throw new HttpDataException(Codes::HTTP_BAD_REQUEST, $e->getData(), 'Invalid data', $e);
    }

    // Persist to database.
    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();

    // Send response.
    return $user;
  }

  /**
   * Deletes a user entity.
   *
   * @Rest\Delete("/{id}", name="api_user_delete")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function deleteAction(Request $request, User $user) {
    $em = $this->getDoctrine()->getManager();
    $em->remove($user);
    $em->flush();

    return $this->view(null, Codes::HTTP_NO_CONTENT);
  }

  /**
   * @Rest\Post("/{user}/group/{group}", name="api_user_group_create")
   *
   * @Rest\QueryParam(name="role", requirements=".+", nullable=true, description="Role to give user in group.")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @param \FOS\RestBundle\Request\ParamFetcherInterface $paramFetcher
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function createUserGroup(Request $request, User $user, Group $group, ParamFetcherInterface $paramFetcher) {
    $em = $this->getDoctrine()->getManager();

    // Get post content.
    $data = $this->getData($request);

    $role = $paramFetcher->get('role');

    // Check if group is already added.
    $userGroup = $em->getRepository(UserGroup::class)->findBy(['user' => $user, 'group' => $group, 'role' => $role]);
    if (!empty($userGroup)) {
      throw new HttpDataException(Codes::HTTP_CONFLICT, $data, 'Group already added');
    }

    $userGroup = new UserGroup();
    $userGroup->setUser($user);
    $userGroup->setGroup($group);
    $userGroup->setRole($role);
    $em->persist($userGroup);
    $em->flush();

    // Send response.
    return $this->createCreatedResponse($userGroup);
  }

  /**
   * @Rest\Put("/{user}/group/{group}", name="api_user_group_update")
   *
   * @Rest\QueryParam(name="role", requirements=".+", nullable=true, description="Role to give user in group.")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\User $user
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function updateUserGroup(Request $request, User $user, Group $group, ParamFetcherInterface $paramFetcher) {
    $em = $this->getDoctrine()->getManager();
    $role = $paramFetcher->get('role');

    // Check if group is already added.
    $userGroup = $em->getRepository(UserGroup::class)->findBy(['user' => $user, 'group' => $group]);
    if (empty($userGroup)) {
      throw new HttpDataException(Codes::HTTP_NOT_FOUND, $paramFetcher->all(), 'User group not found');
    }

    $userGroup = new UserGroup();
    $userGroup->setUser($user);
    $userGroup->setGroup($group);
    $userGroup->setRole($role);
    $em->persist($userGroup);
    $em->flush();

    // Send response.
    return $this->view($userGroup, Codes::HTTP_OK);
  }

}
