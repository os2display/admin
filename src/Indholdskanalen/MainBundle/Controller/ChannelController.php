<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Channel;

/**
 * @Route("/api/channel")
 */
class ChannelController extends Controller {
  /**
   * Save a (new) channel.
   *
   * @Route("")
   * @Method("POST")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ChannelSaveAction(Request $request) {
    // Get posted channel information from the request.
    $post = json_decode($request->getContent());

    if ($post->id) {
      // Load current slide.
      $channel = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Channel')
        ->findOneById($post->id);
    }
    else {
      // This is a new slide.
      $channel = new Channel();
    }

    // Update fields.
    $channel->setTitle($post->title);
    $channel->setOrientation($post->orientation);
    $channel->setCreated($post->created);
    $channel->setSlides($post->slides);

    // Save the entity.
    $em = $this->getDoctrine()->getManager();
    $em->persist($channel);
    $em->flush();

    // Create response data.
    $responseData = array();
    if ($channel) {
      $responseData = array(
        "id" => $channel->getId(),
        "title" => $channel->getTitle(),
        "orientation" => $channel->getOrientation(),
        "created" => $channel->getCreated(),
        "slides" => $channel->getSlides(),
      );
    }

    // Create and return response.
    $response = new Response(json_encode($responseData));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  /**
   * Get channel with $id.
   *
   * @Route("/{id}")
   * @Method("GET")
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ChannelGetAction($id) {
    $channel = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Channel')
      ->findOneById($id);

    // Create response data.
    $responseData = array();
    if ($channel) {
      $responseData = array(
        "id" => $channel->getId(),
        "title" => $channel->getTitle(),
        "orientation" => $channel->getOrientation(),
        "created" => $channel->getCreated(),
        "slides" => $channel->getSlides(),
      );
    }

    // Create and return response.
    $response = new Response(json_encode($responseData));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
