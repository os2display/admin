<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Channel;

/**
 * @Route("/api/channels")
 */
class ChannelsController extends Controller {
  /**
   * Get a list of all channels.
   *
   * @Route("")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ChannelsGetAction() {
    // Get all channel entities.
    $channel_entities = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Channel')
      ->findAll();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($channel_entities) {
      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($channel_entities, 'json');

      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }

  /**
   * Get a bulk of channels.
   *
   * @Route("/bulk")
   * @Method("GET")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ChannelsGetBulkAction(Request $request) {
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
      $response->setContent($serializer->serialize($entities, 'json', SerializationContext::create()->setGroups(array('api'))));
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
