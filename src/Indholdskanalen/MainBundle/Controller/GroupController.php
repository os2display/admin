<?php
/**
 * @file
 * Contains the group controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\Entity\Group;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Group controller.
 *
 * @Route("api/group")
 */
class GroupController extends ApiController {
  /**
   * Lists all group entities.
   *
   * @Route("", name="api_group_index")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();
    $groups = $em->getRepository(Group::class)->findAll();

    return $this->json($groups);
  }

  /**
   * Creates a new group entity.
   *
   * @Route("", name="api_group_new")
   * @Method({"POST"})
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function newAction(Request $request) {
    // Set up new Group.
    $group = new Group();
    $this->setValuesFromRequest($group, $request);

    // Validate entity.
    $errors = $this->validateEntity($group);
    if (count($errors) > 0) {
      return $this->json($errors, 400);
    }

    // Persist to database.
    $em = $this->getDoctrine()->getManager();
    $em->persist($group);
    $em->flush();

    // Send response.
    return $this->json($group, 201);
  }

  /**
   * Finds and displays a group entity.
   *
   * @Route("/{id}", name="api_group_show")
   * @Method("GET")
   *
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function showAction(Group $group) {
    return $this->json($group);
  }

  /**
   * Displays a form to edit an existing group entity.
   *
   * @Route("/{id}", name="api_group_edit")
   * @Method({"PUT"})
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function editAction(Request $request, Group $group) {
    $this->setValuesFromRequest($group, $request);

    // Validate entity.
    $errors = $this->validateEntity($group);
    if (count($errors) > 0) {
      // Send error response.
      return $this->json($errors, 400);
    }

    // Persist to database.
    $em = $this->getDoctrine()->getManager();
    $em->persist($group);
    $em->flush();

    // Send response.
    return $this->json($group);
  }

  /**
   * Deletes a group entity.
   *
   * @Route("/{id}", name="api_group_delete")
   * @Method("DELETE")
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function deleteAction(Request $request, Group $group) {
    $em = $this->getDoctrine()->getManager();
    $em->remove($group);
    $em->flush();

    return new Response(null, 204);
  }
}
