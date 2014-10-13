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

  /**
   * Get a bulk of screens.
   *
   * @Route("/bulk")
   * @Method("GET")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreensGetBulkAction(Request $request) {
    $ids = $request->query->get('ids');

    $response = new Response();

    // Check if slide exists, to update, else create new slide.
    if (isset($ids)) {
      $em = $this->getDoctrine()->getManager();

      $qb = $em->createQueryBuilder();
      $qb->select('m');
      $qb->from('IndholdskanalenMainBundle:Screen', 'm');
      $qb->where($qb->expr()->in('m.id', $ids));

      $result = $qb->getQuery()->getResult();
      $serializer = $this->get('jms_serializer');
      $response->setContent($serializer->serialize($result, 'json'));
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
