<?php
/**
 * @file
 * Contains the group controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Exception\DuplicateEntityException;
use Indholdskanalen\MainBundle\Exception\HttpDataException;
use Indholdskanalen\MainBundle\Exception\ValidationException;
use Indholdskanalen\MainBundle\Security\GroupRoles;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Group controller.
 *
 * @Route("api/group")
 * @Rest\View(serializerGroups={"api"})
 */
class GroupController extends ApiController {
  protected static $editableProperties = ['title'];

  /**
   * Lists all group entities.
   *
   * @Rest\Get("", name="api_group_index")
   * @ApiDoc(
   *   section="Groups",
   *   description="Get all groups",
   *   tags={"group"}
   * )
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function indexAction() {
    $groups = $this->findAll(Group::class);

    foreach ($groups as $group) {
      $group->buildUsers();
    }

    return $this->setApiData($groups);
  }

  /**
   * Creates a new group entity.
   *
   * @Rest\Post("", name="api_group_new")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function newAction(Request $request) {
    $data = $this->getData($request);

    try {
      $group = $this->get('os2display.group_manager')->createGroup($data);
    }
    catch (ValidationException $e) {
      throw new HttpDataException(Codes::HTTP_BAD_REQUEST, $data, 'Invalid data', $e);
    }
    catch (DuplicateEntityException $e) {
      throw new HttpDataException(Codes::HTTP_CONFLICT, $data, 'Duplicate user', $e);
    }

    // Send response.
    return $this->createCreatedResponse($group);
  }

  /**
   * @Rest\Get("/roles")
   * @ApiDoc(
   *   section="Groups",
   *   description="Get all available group roles"
   * )
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return array
   */
  public function getRoles(Request $request) {
    $translator = $this->get('translator');
    $locale = $request->get('locale', $this->getParameter('locale'));

    $roles = GroupRoles::getRoleNames();
    $labels = array_map(function ($role) use ($translator, $locale) {
      return $translator->trans($role, [], 'IndholdskanalenMainBundle', $locale);
    }, $roles);
    $data = array_combine($roles, $labels);
    asort($data);

    return $data;
  }

  /**
   * Finds and displays a group entity.
   *
   * @Rest\Get("/{id}", name="api_group_show")
   *
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @return Group
   */
  public function showAction(Group $group) {
    $group->buildUsers();

    return $group;
  }

  /**
   * Displays a form to edit an existing group entity.
   *
   * @Rest\Put("/{id}", name="api_group_edit")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function editAction(Request $request, Group $group) {
    $data = $this->getData($request);

    try {
      $group = $this->get('os2display.group_manager')->updateGroup($group, $data);
    }
    catch (ValidationException $e) {
      throw new HttpDataException(Codes::HTTP_BAD_REQUEST, $data, 'Invalid data', $e);
    }
    catch (DuplicateEntityException $e) {
      throw new HttpDataException(Codes::HTTP_CONFLICT, $data, 'Duplicate user', $e);
    }

    return $group;
  }

  /**
   * Deletes a group entity.
   *
   * @Rest\Delete("/{id}", name="api_group_delete")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function deleteAction(Request $request, Group $group) {
    $em = $this->getDoctrine()->getManager();
    $em->remove($group);
    $em->flush();

    return $this->view(NULL, Codes::HTTP_NO_CONTENT);
  }

  /**
   * Get users with roles in group.
   *
   * @Rest\Get("/{group}/users")
   */
  public function getGroupUsers(Group $group) {
    $users = $group->buildUsers()->getUsers();

    foreach ($users as $user) {
      $user->buildGroupRoles($group);
    }

    return $this->setApiData($users);
  }

}
