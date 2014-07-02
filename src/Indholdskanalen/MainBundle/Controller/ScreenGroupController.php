<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\ScreenGroup;

/**
 * @Route("/api/screen-group")
 */
class ScreenGroupController extends Controller {
  /**
   * Save a (new) screen group.
   *
   * @Route("")
   * @Method("POST")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreenGroupSaveAction(Request $request) {
    // Get posted screen information from the request.
    $post = json_decode($request->getContent());

    if ($post->id) {
      // Load current slide.
      $screenGroup = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:ScreenGroup')
        ->findOneById($post->id);
    }
    else {
      // This is a new slide.
      $screenGroup = new ScreenGroup();
    }

    // Update fields.
    $screenGroup->setTitle($post->title);
    $screenGroup->setCreated($post->created);

    // Remove groups.
    foreach($screenGroup->getScreens() as $screen) {
      if (!in_array($screen->getId(), $post->screens)) {
        $screenGroup->removeScreen($screen);
      }
    }

    // Add groups.
    foreach($post->screens as $screenId) {
      $screen = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Screen')
        ->findOneById($screenId);
      if ($screen) {
        if (!$screenGroup->getScreens()->contains($screen)) {
          $screenGroup->addScreen($screen);
        }
      }
    }

    // Save the entity.
    $em = $this->getDoctrine()->getManager();
    $em->persist($screenGroup);
    $em->flush();

    // Create the response data.
    $responseData = array(
      "id" => $screenGroup->getId(),
      "title" => $screenGroup->getTitle(),
      "created" => $screenGroup->getCreated(),
      "screens" => $screenGroup->getScreens()
    );

    // Send the json response back to client.
    $response = new Response(json_encode($responseData));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  /**
   * Get screenGroup with ID.
   *
   * @Route("/{id}")
   * @Method("GET")
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreenGroupGetAction($id) {
    $screenGroup = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:ScreenGroup')
      ->findOneById($id);

    $screens = [];
    foreach($screenGroup->getScreens() as $screen) {
      $screens[] = $screen->getId();
    }

    // Create the response data.
    $responseData = array();
    if ($screenGroup) {
      $responseData = array(
        "id" => $screenGroup->getId(),
        "title" => $screenGroup->getTitle(),
        "created" => $screenGroup->getCreated(),
        "screens" => $screens
      );
    }

    // Send the json response back to client.
    $response = new Response(json_encode($responseData));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
