<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Indholdskanalen\MainBundle\Entity\Channel;
use Indholdskanalen\MainBundle\Entity\ChannelSlideOrder;

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

      // If channel is not found, return Not Found
      if (!$channel) {
        $response = new Response();
        $response->setStatusCode(404);

        return $response;
      }
    }
    else {
      // This is a new slide.
      $channel = new Channel();
    }

    // Update fields.
    if (isset($post->title)) {
      $channel->setTitle($post->title);
    }
    if (isset($post->orientation)) {
      $channel->setOrientation($post->orientation);
    }
    if (isset($post->created_at)) {
      $channel->setCreatedAt($post->created_at);
    }

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

    // Get all slide ids from POST.
    $postSlideIds = array();
    foreach($post->slides as $slide) {
      $postSlideIds[] = $slide->id;
    }

    // Remove slides.
    foreach($channel->getChannelSlideOrders() as $channelSlideOrder) {
      $slide = $channelSlideOrder->getChannel();

      if (!in_array($slide->getId(), $postSlideIds)) {
        $channelSlideOrder->getChannel()->removeChannelSlideOrder($channelSlideOrder);
        $channelSlideOrder->getSlide()->removeChannelSlideOrder($channelSlideOrder);

        $em->persist($channelSlideOrder->getChannel());
        $em->persist($channelSlideOrder->getSlide());

        $em->remove($channelSlideOrder);
        $em->flush();
      }
    }

    // Save the entity.
    $em->persist($channel);
    $em->flush();

    // Add slides and update sort order.
    $sortOrder = 0;
    foreach($postSlideIds as $slideId) {
      $slide = $doctrine->getRepository('IndholdskanalenMainBundle:Slide')->findOneById($slideId);

      $channelSlideOrder = $doctrine->getRepository('IndholdskanalenMainBundle:ChannelSlideOrder')->findOneBy(
        array(
          'channel' => $channel,
          'slide' => $slide,
        )
      );
      if (!$channelSlideOrder) {
        $channelSlideOrder = new ChannelSlideOrder();
        $channelSlideOrder->setChannel($channel);
        $channelSlideOrder->setSlide($slide);
      }

      $channelSlideOrder->setSortOrder($sortOrder);
      $sortOrder++;

      // Save the ChannelSlideOrder.
      $em->persist($channelSlideOrder);
      $em->flush();
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

    $serializer = $this->get('jms_serializer');

    // Create response.
    $response = new Response();
    if ($channel) {
      // Get slides.
      $slides = array();
      foreach($channel->getChannelSlideOrders() as $channelSlideOrder) {
        $slides[] = json_decode($serializer->serialize($channelSlideOrder->getSlide(), 'json'));
      }

      // Create json content.
      $jsonContent = $serializer->serialize($channel, 'json');

      // Attach extra fields.
      $ob = json_decode($jsonContent);
      $ob->slides = $slides;
      $jsonContent = json_encode($ob);

      $response->headers->set('Content-Type', 'application/json');
      $response->setContent($jsonContent);
    }
    else {
      $response->setStatusCode(404);
    }

    return $response;
  }
}
