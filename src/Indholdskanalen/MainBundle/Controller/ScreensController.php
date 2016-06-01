<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Screen;
use JMS\Serializer\SerializationContext;

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
  public function screensGetAction() {
    // Screen entities
    $screen_entities = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Screen')
      ->findAll();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

	  $serializer = $this->get('jms_serializer');
	  $response->setContent($serializer->serialize($screen_entities, 'json', SerializationContext::create()->setGroups(array('api-bulk'))->enableMaxDepthChecks()));

    return $response;
  }

  /**
   * Get a bulk of screens.
   *
   * The order in which the screen id's is set in the query string is the same
   * order in which they are returned.
   *
   * @Route("/bulk")
   * @Method("GET")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function screensGetBulkAction(Request $request) {
    $ids = $request->query->get('ids');

    $response = new Response();

    if (isset($ids)) {
      $em = $this->getDoctrine()->getManager();

      // Create query to load the entities.
      $qb = $em->createQueryBuilder();
      $qb->select('s');
      $qb->from('IndholdskanalenMainBundle:Screen', 's');
      $qb->where($qb->expr()->in('s.id', $ids));
      $results = $qb->getQuery()->getResult();

      // Sort the entities based on the order of the ids given in the
      // parameters.
      // @todo: Use mysql order by FIELD('id',1,4,2)....
      $entities = array();
      foreach ($ids as $id) {
        foreach ($results as $index => $entity) {
          if ($entity->getId() == $id) {
            $entities[] = $entity;
            unset($results[$index]);
          }
        }
      }

      $serializer = $this->get('jms_serializer');
      $response->setContent($serializer->serialize($entities, 'json', SerializationContext::create()->setGroups(array('api-bulk'))->enableMaxDepthChecks()));
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
