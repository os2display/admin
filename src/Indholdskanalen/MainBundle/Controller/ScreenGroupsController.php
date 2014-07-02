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

    // Build our screen array.
    $screenGroups = array();
    foreach ($screenGroupEntities as $screenGroup) {
      $screenGroups[] = array(
        'id' => $screenGroup->getId(),
        'title' => $screenGroup->getTitle(),
        'created' => $screenGroup->getCreated(),
        'screens' => $screenGroup->getScreens()
      );
    }

    // Create and return response
    $response = new Response(json_encode($screenGroups));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
