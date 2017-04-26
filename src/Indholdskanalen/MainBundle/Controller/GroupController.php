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
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    $post = json_decode($request->getContent());

    $group = new Group();
    $group->setTitle(isset($post->title)?: null);

    // Validate entity.
    $validator = $this->get('validator');
    $errors = $validator->validate($group);

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
   */
  public function showAction(Group $group) {
    $deleteForm = $this->createDeleteForm($group);

    return $this->render('group/show.html.twig', array(
      'group' => $group,
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing group entity.
   *
   * @Route("/{id}/edit", name="api_group_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, Group $group) {
    $deleteForm = $this->createDeleteForm($group);
    $editForm = $this->createForm('Indholdskanalen\MainBundle\Form\GroupType', $group);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('api_group_edit', array('id' => $group->getId()));
    }

    return $this->render('group/edit.html.twig', array(
      'group' => $group,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a group entity.
   *
   * @Route("/{id}", name="api_group_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, Group $group) {
    $form = $this->createDeleteForm($group);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($group);
      $em->flush();
    }

    return $this->redirectToRoute('api_group_index');
  }

  /**
   * Creates a form to delete a group entity.
   *
   * @param Group $group The group entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Group $group) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('api_group_delete', array('id' => $group->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
