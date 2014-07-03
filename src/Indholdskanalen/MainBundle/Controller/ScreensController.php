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

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($screen_entities) {
      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($screen_entities, 'json');

      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
