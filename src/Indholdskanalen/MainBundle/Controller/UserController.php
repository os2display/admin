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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
   * @Rest\QueryParam(
   *   name="filter",
   *   description="Filter to apply",
   *   requirements="string",
   *   array=true,
   *   nullable=true
   * )
   * @ApiDoc(
   *   section="Users",
   *   description="Returns all users",
   *   resource=false,
   *   filters={
   *      {"name"="filter", "dataType"="string"}
   *   },
   *   statusCodes={
   *     200="Success"
   *   }
   * )
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
   * @ApiDoc(
   *   section="Users",
   *   description="Create user",
   *   statusCodes={
   *     201="User created",
   *     400="Invalid user data",
   *     409="Duplicate user (specified email/username already used)"
   *   }
   * )
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
   * @ApiDoc(
   *   section="User",
   *   description="Get current user"
   * )
   *
   * @return User
   */
  public function getCurrentUser() {
    $user = $this->getUser();

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
   * @Rest\RequestParam(
   *   name="role",
   *   description="Role to give user in group.",
   *   requirements="string",
   *   nullable=true
   * )
   * @ApiDoc(
   *   section="Users and groups",
   *   description="Add user to group",
   *   documentation="Add user to group"
   * )
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
   * @ApiDoc(
	 *   section="Users and groups",
   *   description=""
   * )
   *
   * @Rest\RequestParam(
   *   name="role",
   *   description="Role to give user in group.",
   *   requirements=".+",
   *   nullable=true
   * )
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
