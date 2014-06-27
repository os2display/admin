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

    // Create response data.
    $channels = array();
    foreach ($channel_entities as $channel) {
      $channels[] = array(
        'id' => $channel->getId(),
        'title' => $channel->getTitle(),
        'orientation' => $channel->getOrientation(),
        'created' => $channel->getCreated(),
        'slides' => $channel->getSlides(),
      );
    }

    // Create and return response.
    $response = new Response(json_encode($channels));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
