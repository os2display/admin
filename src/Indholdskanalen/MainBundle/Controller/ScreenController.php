<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Screen;

/**
 * @Route("/api/screen")
 */
class ScreenController extends Controller {
  /**
   * Save a (new) screen.
   *
   * @Route("")
   * @Method("POST")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreenSaveAction(Request $request) {
    // Get posted screen information from the request.
    $post = json_decode($request->getContent());

    if ($post->id) {
      // Load current slide.
      $screen = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Screen')
        ->findOneById($post->id);
    }
    else {
      // This is a new slide.
      $screen = new Screen();
    }

    // Update fields.
    $screen->setTitle($post->title);
    $screen->setOrientation($post->orientation);
    $screen->setCreated($post->created);
    $screen->setWidth($post->width);
    $screen->setHeight($post->height);
    $screen->setGroups($post->groups);

    // Save the entity.
    $em = $this->getDoctrine()->getManager();
    $em->persist($screen);
    $em->flush();

    // Create the response data.
    $responseData = array(
      "id" => $screen->getId(),
      "title" => $screen->getTitle(),
      "orientation" => $screen->getOrientation(),
      "created" => $screen->getCreated(),
      "width" => $screen->getWidth(),
      "height" => $screen->getHeight(),
      "groups" => $screen->getGroups()
    );

    // Send the json response back to client.
    $response = new Response(json_encode($responseData));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  /**
   * Get screen with ID.
   *
   * @Route("/{id}")
   * @Method("GET")
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreenGetAction($id) {
    $screen = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Screen')
      ->findOneById($id);

    // Create the response data.
    $responseData = array();
    if ($screen) {
      $responseData = array(
        "id" => $screen->getId(),
        "title" => $screen->getTitle(),
        "orientation" => $screen->getOrientation(),
        "created" => $screen->getCreated(),
        "width" => $screen->getWidth(),
        "height" => $screen->getHeight(),
        "groups" => $screen->getGroups()
      );
    }

    // Send the json response back to client.
    $response = new Response(json_encode($responseData));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
