<?php

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\CustomJsonResponse;
use Indholdskanalen\MainBundle\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Group controller.
 *
 * @Route("api/group")
 */
class GroupController extends Controller {
  /**
   * Lists all group entities.
   *
   * @Route("/", name="api_group_index")
   * @Method("GET")
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
   * @Route("/new", name="api_group_new")
   * @Method({"POST"})
   */
  public function newAction(Request $request) {
    $entityService = $this->get('os2display.entity_service');

    // Get post content.
    $post = json_decode($request->getContent());

    $group = new Group();

    // Set values from request.
    $entityService->setValues($group, $post);

    // Validate entity.
    $errors = $entityService->validateEntity($group);
    if (count($errors) > 0) {
      $response = new CustomJsonResponse(400);
      $response->setData($errors, $this->get('jms_serializer'));
      return $response;
    }

    $em = $this->getDoctrine()->getManager();
    $em->persist($group);
    $em->flush();

    $response = new CustomJsonResponse();
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
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function showAction(Group $group) {
    $response = new CustomJsonResponse();
    $response->setData($group, $this->get('jms_serializer'), ['api']);
    return $response;
  }

  /**
   * Displays a form to edit an existing group entity.
   *
   * @Route("/{id}/edit", name="api_group_edit")
   * @Method({"POST"})
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Indholdskanalen\MainBundle\Entity\Group $group
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function editAction(Request $request, Group $group) {
    $entityService = $this->get('os2display.entity_service');

    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    // Get post content.
    $post = json_decode($request->getContent());

    // Set values from request.
    $entityService->setValues($group, $post);

    // Validate entity.
    $errors = $entityService->validateEntity($group);
    if (count($errors) > 0) {
      $response = new CustomJsonResponse(400);
      $response->setData($errors, $this->get('jms_serializer'));
      return $response;
    }

    $em = $this->getDoctrine()->getManager();
    $em->persist($group);
    $em->flush();

    $response = new CustomJsonResponse();
    $response->setJsonData(json_encode(['id' => $group->getId()]));
    return $response;
  }

  /**
   * Deletes a group entity.
   *
   * @Route("/{id}", name="api_group_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, Group $group) {
    $em = $this->getDoctrine()->getManager();
    $em->remove($group);
    $em->flush();

    return new CustomJsonResponse();
  }
}
