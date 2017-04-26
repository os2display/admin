<?php

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
    $serializer = $this->get('jms_serializer');
    $em = $this->getDoctrine()->getManager();

    $groups = $em->getRepository('IndholdskanalenMainBundle:Group')->findAll();

    $data = json_decode($serializer->serialize($groups, 'json', SerializationContext::create()
      ->setGroups(array('api'))
      ->enableMaxDepthChecks()));

    return new JsonResponse($data);
  }

  /**
   * Creates a new group entity.
   *
   * @Route("/new", name="api_group_new")
   * @Method({"POST"})
   */
  public function newAction(Request $request) {
    $entityService = $this->get('os2display.entity_service');

    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    // Get post content.
    $post = json_decode($request->getContent());

    $group = new Group();

    // Set values from request.
    $entityService->setValues($group, $post);

    // Validate entity.
    $errors = $entityService->validateEntity($group);
    if (count($errors) > 0) {
      $serializer = $this->get('jms_serializer');
      $data = $serializer->serialize($errors, 'json', SerializationContext::create());

      $response->setContent($data);
      $response->setStatusCode(400);
      return $response;
    }

    $em = $this->getDoctrine()->getManager();
    $em->persist($group);
    $em->flush();

    $response->setContent(json_encode(['id' => $group->getId()]));
    $response->setStatusCode(400);
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
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    $serializer = $this->get('jms_serializer');
    $data = $serializer->serialize($group, 'json', SerializationContext::create()
      ->setGroups(array('api'))
      ->enableMaxDepthChecks());

    $response->setContent($data);
    $response->setStatusCode(200);
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
      $serializer = $this->get('jms_serializer');
      $data = $serializer->serialize($errors, 'json', SerializationContext::create());

      $response->setContent($data);
      $response->setStatusCode(400);
      return $response;
    }

    $em = $this->getDoctrine()->getManager();
    $em->persist($group);
    $em->flush();

    $response->setContent(json_encode(['id' => $group->getId()]));
    $response->setStatusCode(400);
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

    return new JsonResponse(null, 200);
  }
}
