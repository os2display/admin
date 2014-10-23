<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;

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

      // If channel is not found, return Not Found.
      if (!$channel) {
        $response = new Response();
        $response->setStatusCode(404);

        return $response;
      }
    }
    else {
      // This is a new channel.
      $channel = new Channel();
      $channel->setCreatedAt(time());
    }

    // Update fields.
    if (isset($post->title)) {
      $channel->setTitle($post->title);
    }
    if (isset($post->orientation)) {
      $channel->setOrientation($post->orientation);
    }

    // Remove screens.
    foreach ($channel->getScreens() as $screen) {
      if (!in_array($screen, $post->screens)) {
        $channel->removeScreen($screen);
      }
    }

    // Add screens.
    foreach ($post->screens as $screen) {
      $screen = $doctrine->getRepository('IndholdskanalenMainBundle:Screen')
        ->findOneById($screen->id);
      if ($screen) {
        if (!$channel->getScreens()->contains($screen)) {
          $channel->addScreen($screen);
        }
      }
    }

    // Get all slide ids from POST.
    $post_slide_ids = array();
    foreach ($post->slides as $slide) {
      $post_slide_ids[] = $slide->id;
    }

    // Remove slides.
    foreach ($channel->getChannelSlideOrders() as $channel_slide_order) {
      $slide = $channel_slide_order->getSlide();

      if (!in_array($slide->getId(), $post_slide_ids)) {
        $channel->removeChannelSlideOrder($channel_slide_order);
      }
    }

    // Add slides and update sort order.
    $sort_order = 0;
    foreach ($post_slide_ids as $slide_id) {
      $slide = $doctrine->getRepository('IndholdskanalenMainBundle:Slide')->findOneById($slide_id);

      $channel_slide_order = $doctrine->getRepository('IndholdskanalenMainBundle:ChannelSlideOrder')->findOneBy(
        array(
          'channel' => $channel,
          'slide' => $slide,
        )
      );
      if (!$channel_slide_order) {
        // New ChannelSLideOrder.
        $channel_slide_order = new ChannelSlideOrder();
        $channel_slide_order->setChannel($channel);
        $channel_slide_order->setSlide($slide);
        $em->persist($channel_slide_order);

        // Associate Order to Channel.
        $channel->addChannelSlideOrder($channel_slide_order);
      }

      $channel_slide_order->setSortOrder($sort_order);
      $sort_order++;
    }

    // Save the entity.
    $em->persist($channel);
    $em->flush();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($channel) {
      $serializer = $this->get('jms_serializer');
      $json_content = $serializer->serialize($channel, 'json');

      $response->setContent($json_content);
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
   * @param int $id
   *   Channel id of the channel to delete.
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
      $response->headers->set('Content-Type', 'application/json');
      $json_content = $serializer->serialize($channel, 'json', SerializationContext::create()->setGroups(array('api')));
      $response->setContent($json_content);
    }
    else {
      $response->setStatusCode(404);
    }

    return $response;
  }

  /**
   * Delete channel.
   *
   * @Route("/{id}")
   * @Method("DELETE")
   *
   * @param int $id
   *   Channel id of the channel to delete.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ChannelDeleteAction($id) {
    $channel = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Channel')
      ->findOneById($id);

    // Create response.
    $response = new Response();

    if ($channel) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($channel);
      $em->flush();

      // Element deleted.
      $response->setStatusCode(200);
    }
    else {
      // Not found.
      $response->setStatusCode(404);
    }

    return $response;
  }
}
