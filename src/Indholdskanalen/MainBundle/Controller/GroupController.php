<?php
/**
 * @file
 * Contains the group controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Exception\HttpDataException;
use Indholdskanalen\MainBundle\Exception\ValidationException;
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
    $em = $this->getDoctrine()->getManager();
    $groups = $em->getRepository(Group::class)->findAll();

    return $groups;
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
    // Set up new Group.
    $group = new Group();
    $this->setValuesFromRequest($group, $request, static::$editableProperties);

    try {
      $this->validateEntity($group);
    } catch (ValidationException $e) {
      throw new HttpDataException(Codes::HTTP_BAD_REQUEST, $e->getData(), 'Invalid data', $e);
    }

    // Persist to database.
    $em = $this->getDoctrine()->getManager();
    $em->persist($group);
    $em->flush();

    // Send response.
    return $this->createCreatedResponse($group);
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
    $this->setValuesFromRequest($group, $request, static::$editableProperties);

    try {
      $this->validateEntity($group);
    } catch (ValidationException $e) {
      throw new HttpDataException(Codes::HTTP_BAD_REQUEST, $e->getData(), 'Invalid data', $e);
    }

    // Persist to database.
    $em = $this->getDoctrine()->getManager();
    $em->persist($group);
    $em->flush();

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

    return $this->view(null, Codes::HTTP_NO_CONTENT);
  }
}
