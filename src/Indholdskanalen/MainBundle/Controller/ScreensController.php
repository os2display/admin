<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Screen;

/**
 * @Route("/api/screens")
 */
class ScreensController extends Controller {
  /**
   * Get a list of all screens.
   *
   * @Route("")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreensGetAction() {
    // Screen entities
    $screen_entities = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Screen')
      ->findAll();

    // Build our screen array.
    $screens = array();
    foreach ($screen_entities as $screen) {
      $screens[] = array(
        'id' => $screen->getId(),
        'title' => $screen->getTitle(),
        'orientation' => $screen->getOrientation(),
        'width' => $screen->getWidth(),
        'height' => $screen->getHeight(),
        'created' => $screen->getCreated(),
        'groups' => $screen->getGroups()
      );
    }

    // Create and return response
    $response = new Response(json_encode($screens));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
