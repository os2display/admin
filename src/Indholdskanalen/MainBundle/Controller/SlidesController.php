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

    // Build our slide array.
    $slides = array();
    foreach ($slide_entities as $slide) {
      $slides[] = array(
        'id' => $slide->getId(),
        'title' => $slide->getTitle(),
        'orientation' => $slide->getOrientation(),
        'template' => $slide->getTemplate(),
        'created' => $slide->getCreated(),
        'options' => unserialize($slide->getOptions()),
      );
    }

    $response = new Response(json_encode($slides));
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }
}
