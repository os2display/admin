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

    $doctrine = $this->getDoctrine();
    $em = $this->getDoctrine()->getManager();

    if ($post->id) {
      // Load current slide.
      $channel = $doctrine->getRepository('IndholdskanalenMainBundle:Channel')
        ->findOneById($post->id);
    }
    else {
      // This is a new slide.
      $channel = new Channel();
    }

    // Update fields.
    $channel->setTitle($post->title);
    $channel->setOrientation($post->orientation);
    $channel->setCreatedAt($post->created_at);

    // Remove screens.
    foreach($channel->getScreens() as $screen) {
      if (!in_array($screen, $post->screens)) {
        $channel->removeScreen($screen);
      }
    }

    // Add screens.
    foreach($post->screens as $screen) {
      $screen = $doctrine->getRepository('IndholdskanalenMainBundle:Screen')
        ->findOneById($screen->id);
      if ($screen) {
        if (!$channel->getScreens()->contains($screen)) {
          $channel->addScreen($screen);
        }
      }
    }

    // Save the entity.
    $em->persist($channel);
    $em->flush();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($channel) {
      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($channel, 'json');

      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

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

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($channel) {
      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($channel, 'json');

      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
