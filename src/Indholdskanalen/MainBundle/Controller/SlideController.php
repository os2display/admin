<?php

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\Entity\ChannelSlideOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;

use Indholdskanalen\MainBundle\Entity\Slide;


/**
 * @Route("/api/slide")
 */
class SlideController extends Controller {
  /**
   * Save a (new) slide.
   *
   * @Route("")
   * @Method("POST")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SlideSaveAction(Request $request) {
    // Get posted slide information from the request.
    $post = json_decode($request->getContent(), TRUE);

    $doctrine = $this->getDoctrine();
    $em = $doctrine->getManager();

    if ($post['id']) {
      // Load current slide.
      $slide = $doctrine->getRepository('IndholdskanalenMainBundle:Slide')
        ->findOneById($post['id']);
    }
    else {
      // This is a new slide.
      $slide = new Slide();
    }

    // Get user
    $userEntity = $this->get('security.context')->getToken()->getUser();

    // Update fields.
    $slide->setTitle($post['title']);
    $slide->setOrientation($post['orientation']);
    $slide->setTemplate($post['template']);
    $slide->setCreatedAt($post['created_at']);
    $slide->setOptions($post['options']);
    $slide->setUser($userEntity->getId());
    $slide->setDuration($post['duration']);

    $postChannelIds = array();
    foreach($post['channels'] as $channel) {
      $postChannelIds[] = $channel['id'];
    }

    foreach($slide->getChannelSlideOrders() as $channelSlideOrder) {
      $channel = $channelSlideOrder->getChannel();

      if (!in_array($channel->getId(), $postChannelIds)) {
        $channelSlideOrder->getChannel()->removeChannelSlideOrder($channelSlideOrder);
        $channelSlideOrder->getSlide()->removeChannelSlideOrder($channelSlideOrder);

        $em->persist($channelSlideOrder->getChannel());
        $em->persist($channelSlideOrder->getSlide());

        $em->remove($channelSlideOrder);
        $em->flush();
      }
    }

    foreach($post['channels'] as $channel) {
      $channel = $doctrine->getRepository('IndholdskanalenMainBundle:Channel')->findOneById($channel['id']);

      $channelSlideOrder = $doctrine->getRepository('IndholdskanalenMainBundle:ChannelSlideOrder')->findOneBy(
        array(
          'channel' => $channel,
          'slide' => $slide,
        )
      );
      if (!$channelSlideOrder) {
        $channelLargestSortOrder = $doctrine->getRepository('IndholdskanalenMainBundle:ChannelSlideOrder')->findOneBy(
          array(
            'channel' => $channel
          ),
          array(
            'sortOrder' => 'DESC'
          )
        );
        $index = 0;
        if ($channelLargestSortOrder) {
          $index = $channelLargestSortOrder->getSortOrder();
        }

        $channelSlideOrder = new ChannelSlideOrder();
        $channelSlideOrder->setChannel($channel);
        $channelSlideOrder->setSlide($slide);
        $channelSlideOrder->setSortOrder($index + 1);

        $em->persist($channelSlideOrder);
        $em->flush();
      }
    }

    // Save the entity.
    $em->persist($slide);
    $em->flush();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    $serializer = $this->get('jms_serializer');
    $jsonContent = $serializer->serialize($slide, 'json');

    $response->setContent($jsonContent);

    return $response;
  }

  /**
   * Get slide with ID.
   *
   * @Route("/{id}")
   * @Method("GET")
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SlideGetAction($id) {
    $slide = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Slide')
      ->findOneById($id);

    // Get the serializer
    $serializer = $this->get('jms_serializer');

    // Create response.
    $response = new Response();
    if ($slide) {
      // Get handle to media.
      $sonataMedia = $this->getDoctrine()->getRepository('ApplicationSonataMediaBundle:Media');

      $channels = array();

      foreach($slide->getChannelSlideOrders() as $channelSlideOrder) {
        $channels[] = json_decode($serializer->serialize($channelSlideOrder->getChannel(), 'json'));
      }
      // Add channels to slide.
/*      $sortOrderIterator = $slide->getChannelSlideOrders()->getIterator();

      $sortOrderIterator->uasort(function ($first, $second) {
        return (int) $first->getSortOrder() > (int) $second->getSortOrder() ? 1 : -1;
      });

      foreach(new ArrayCollection(iterator_to_array($sortOrderIterator)) as $sortOrderItem) {
        $channels[] = $serializer->serialize($sortOrderItem->getChannel(), 'json');
      }*/

      // Add image urls to result.
      $imageUrls = array();
      if (isset($slide->getOptions()['images'])) {
        $imageIds = $slide->getOptions()['images'];
        foreach ($imageIds as $imageId) {
          $image = $sonataMedia->findOneById($imageId);

          if ($image) {
            $serializer = $this->get('jms_serializer');
            $jsonContent = $serializer->serialize($image, 'json');

            $content = json_decode($jsonContent);

            $imageUrls[$imageId] = $content->urls;
          }
        }
      }

      // Add image urls to result.
      $videoUrls = array();
      if (isset($slide->getOptions()['videos'])) {
        $videoIds = $slide->getOptions()['videos'];
        foreach ($videoIds as $videoId) {
          $video = $sonataMedia->findOneById($videoId);

          if ($video) {
            $serializer = $this->get('jms_serializer');
            $jsonContent = $serializer->serialize($video, 'json');

            $content = json_decode($jsonContent);

            $urls = array(
              'thumbnail' => $content->provider_metadata[0]->thumbnails[1]->reference,
              'mp4' => $content->provider_metadata[0]->reference,
              'ogg' => $content->provider_metadata[1]->reference,
            );

            $videoUrls[$videoId] = $urls;
          }
        }
      }

      $jsonContent = $serializer->serialize($slide, 'json');

      // Add extra data slide
      $ob = json_decode($jsonContent);

      $ob->imageUrls = $imageUrls;
      $ob->videoUrls = $videoUrls;
      $ob->channels = $channels;

      $jsonContent = json_encode($ob);

      // Return slide.
      $response->headers->set('Content-Type', 'application/json');
      $response->setContent($jsonContent);
    }
    else {
      // Not found.
      $response->setStatusCode(404);
    }

    return $response;
  }
}
