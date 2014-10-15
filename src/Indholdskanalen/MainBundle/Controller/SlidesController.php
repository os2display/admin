<?php

namespace Indholdskanalen\MainBundle\Controller;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Slide;
use JMS\Serializer\SerializationContext;

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
      $jsonContent = $serializer->serialize($slide_entities, 'json', SerializationContext::create()->setGroups(array('api')));

      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }

  /**
   * Get a bulk of slides.
   *
   * @Route("/bulk")
   * @Method("GET")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SlidesGetBulkAction(Request $request) {
    $ids = $request->query->get('ids');

    $response = new Response();

    // Check if slide exists, to update, else create new slide.
    if (isset($ids)) {
      $em = $this->getDoctrine()->getManager();

      // Create query to load the entities.
      $qb = $em->createQueryBuilder();
      $qb->select('m');
      $qb->from('IndholdskanalenMainBundle:Slide', 'm');
      $qb->where($qb->expr()->in('m.id', $ids));
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
      $response->headers->set('Content-Type', 'application/json');
      $response->setContent($serializer->serialize($entities, 'json', SerializationContext::create()->setGroups(array('api'))));
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
