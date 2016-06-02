<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Channel;
use JMS\Serializer\SerializationContext;

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
  public function channelsGetAction() {
    // Get all channel entities.
    $channel_entities = $this->getDoctrine()
      ->getRepository('IndholdskanalenMainBundle:Channel')
      ->findAll();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    $serializer = $this->get('jms_serializer');
    $response->setContent($serializer->serialize($channel_entities, 'json', SerializationContext::create()
          ->setGroups(array('api-bulk'))
          ->enableMaxDepthChecks()));

    return $response;
  }
}
