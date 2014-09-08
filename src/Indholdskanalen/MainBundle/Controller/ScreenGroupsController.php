<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\ScreenGroup;

/**
 * @Route("/api/screen-groups")
 */
class ScreenGroupsController extends Controller {
  /**
   * Get a list of all screen groups.
   *
   * @Route("")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreenGroupsGetAction() {
    // Screen entities
    $screenGroupEntities = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:ScreenGroup')
      ->findAll();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($screenGroupEntities) {
      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($screenGroupEntities, 'json');

      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
