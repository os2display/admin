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
 * @Route("/api/timeline")
 */
class TimelineController extends Controller {
  /**
   * Get a bulk of screens with channels.
   *
   * @Route("/screens-bulk")
   * @Method("GET")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function screensGetChannelBulkAction(Request $request) {
    $ids = $request->query->get('ids');

    $response = new Response();

    // Check if slide exists, to update, else create new slide.
    if (isset($ids)) {
      $em = $this->getDoctrine()->getManager();

      // Create query to load the entities.
      $qb = $em->createQueryBuilder();
      $qb->select('m');
      $qb->from('IndholdskanalenMainBundle:Screen', 'm');
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
      $response->setContent($serializer->serialize($entities, 'json', SerializationContext::create()->setGroups(array('api'))->enableMaxDepthChecks()));
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }

  /**
   * Get a bulk of screens with channels.
   *
   * @Route("/channels-bulk")
   * @Method("GET")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function channelsGetChannelBulkAction(Request $request) {
    $ids = $request->query->get('ids');

    $response = new Response();

    // Check if slide exists, to update, else create new slide.
    if (isset($ids)) {
      $em = $this->getDoctrine()->getManager();

      // Create query to load the entities.
      $qb = $em->createQueryBuilder();
      $qb->select('m');
      $qb->from('IndholdskanalenMainBundle:Channel', 'm');
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
      $response->setContent($serializer->serialize($entities, 'json', SerializationContext::create()->setGroups(array('api'))->enableMaxDepthChecks()));
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
