<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Slide;

/**
 * @Route("/api/slide")
 */
class SlideController extends Controller {
  /**
   * Save a (new) slide.
   *
   * @Route("")
   * @Method("POST")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SlideSaveAction(Request $request) {
    // Get posted slide information from the request.
    $post = json_decode($request->getContent(), TRUE);

    if ($post['id']) {
      // Load current slide.
      $slide = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Slide')
        ->findOneById($post['id']);
    }
    else {
      // This is a new slide.
      $slide = new Slide();
    }

    // Update fields.
    $slide->setTitle($post['title']);
    $slide->setOrientation($post['orientation']);
    $slide->setTemplate($post['template']);
    $slide->setCreated($post['created']);
    $slide->setOptions($post['options']);
    $slide->setUser($post['user']);

    // Save the entity.
    $em = $this->getDoctrine()->getManager();
    $em->persist($slide);
    $em->flush();

    // Create the response data.
    $responseData = array(
      "id" => $slide->getId(),
      "title" => $slide->getTitle(),
      "orientation" => $slide->getOrientation(),
      "template" => $slide->getTemplate(),
      "created" => $slide->getCreated(),
      "options" => $slide->getOptions(),
      "user" => $slide->getUser(),
    );

    // Send the json response back to client.
    $response = new Response(json_encode($responseData));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  /**
   * Get slide with ID.
   *
   * @Route("/{id}")
   * @Method("GET")
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SlideGetAction($id) {
    $slide = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Slide')
      ->findOneById($id);

    $responseData = array();

    if ($slide) {
      $responseData = array(
        "id" => $slide->getId(),
        "title" => $slide->getTitle(),
        "orientation" => $slide->getOrientation(),
        "template" => $slide->getTemplate(),
        "created" => $slide->getCreated(),
        "options" => $slide->getOptions(),
        "user" => $slide->getUser(),
      );
    }

    $response = new Response(json_encode($responseData));
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }
}
