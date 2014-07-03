<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Slide;

/**
 * @Route("/api/slides")
 */
class SlidesController extends Controller {
  /**
   * Get a list of all slides.
   *
   * @Route("")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SlidesGetAction() {
    // Slide entities
    $slide_entities = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Slide')
      ->findAll();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($slide_entities) {
      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($slide_entities, 'json');

      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
