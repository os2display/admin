<?php
/**
 * @file
 * Contains the group controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\CustomJsonResponse;
use Indholdskanalen\MainBundle\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Group controller.
 *
 * @Route("api/group")
 */
class GroupController extends Controller {
  /**
   * Lists all group entities.
   *
   * @Route("", name="api_group_index")
   * @Method("GET")
   *
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $groups = $em->getRepository('IndholdskanalenMainBundle:Group')->findAll();

    $response = new CustomJsonResponse();
    $response->setData($groups, $this->get('jms_serializer'), ['api']);
    return $response;
  }

  /**
   * Creates a new group entity.
   *
   * @Route("", name="api_group_new")
   * @Method({"POST"})
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function newAction(Request $request) {
    // Set up new Group.
    $group = new Group();

    // Get the Entity Service.
    $entityService = $this->get('os2display.entity_service');

    // Get post content.
    $post = json_decode($request->getContent());

    // Set values from request.
    $entityService->setValues($group, $post);

    // Validate entity.
    $errors = $entityService->validateEntity($group);
    if (count($errors) > 0) {
      // Send error response.
      $response = new CustomJsonResponse(400);
      $response->setData($errors, $this->get('jms_serializer'));
      return $response;
    }

    // Persist to database.
    $em = $this->getDoctrine()->getManager();
    $em->persist($group);
    $em->flush();

    // Send response.
    $response = new CustomJsonResponse(201);
    $response->setJsonData(json_encode(['id' => $group->getId()]));
    return $response;
  }

  /**
   * Finds and displays a group entity.
   *
   * @Route("/{id}", name="api_group_show")
   * @Method("GET")
   *
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function showAction(Group $group) {
    $response = new CustomJsonResponse();
    $response->setData($group, $this->get('jms_serializer'), ['api']);
    return $response;
  }

  /**
   * Displays a form to edit an existing group entity.
   *
   * @Route("/{id}", name="api_group_edit")
   * @Method({"PUT"})
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @return \Indholdskanalen\MainBundle\CustomJsonResponse
   */
  public function editAction(Request $request, Group $group) {
    // Get the Entity Service.
    $entityService = $this->get('os2display.entity_service');

    // Get post content.
    $post = json_decode($request->getContent());

    // Set values from request.
    $entityService->setValues($group, $post);

    // Validate entity.
    $errors = $entityService->validateEntity($group);
    if (count($errors) > 0) {
      // Send error response.
      $response = new CustomJsonResponse(400);
      $response->setData($errors, $this->get('jms_serializer'));
      return $response;
    }

    // Persist to database.
    $em = $this->getDoctrine()->getManager();
    $em->persist($group);
    $em->flush();

    // Send response.
    $response = new CustomJsonResponse();
    $response->setJsonData(json_encode(['id' => $group->getId()]));
    return $response;
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

    return new CustomJsonResponse(204);
  }
}
