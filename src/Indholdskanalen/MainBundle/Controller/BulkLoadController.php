<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/api/bulk")
 */
class BulkLoadController extends Controller {

  /**
   * Parse the type to full data type.
   *
   * @param $type
   * @return string
   */
  private function parseType($type) {
    $typeToLower = strtolower($type);

    switch ($typeToLower) {
      case 'slide':
        return 'IndholdskanalenMainBundle:Slide';
      case 'channel':
        return 'IndholdskanalenMainBundle:Channel';
      case 'screen':
        return 'IndholdskanalenMainBundle:Screen';
      case 'media':
        return 'ApplicationSonataMediaBundle:Media';
      default:
        return 'IndholdskanalenMainBundle:' . $type;
    }
  }

  /**
   * Get a bulk of screens.
   *
   * The order in which the screen id's is set in the query string is the same
   * order in which they are returned.
   *
   * @TODO: REVIEW - Add cache to query
   *
   * @Route("/{type}/{serializationGroup}")
   * @Method("GET")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function screensGetBulkAction(Request $request, $type, $serializationGroup) {
    $ids = $request->query->get('ids');

    $response = new Response();

    if (isset($ids)) {
      $em = $this->getDoctrine()->getManager();

      // Create query to load the entities.
      $qb = $em->createQueryBuilder();
      $qb->select('i');
      $qb->from($this->parseType($type), 'i');
      $qb->where($qb->expr()->in('i.id', $ids));
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
      $response->setContent($serializer->serialize($entities, 'json', SerializationContext::create()->setGroups(array($serializationGroup))->enableMaxDepthChecks()));
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
