<?php

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\Entity\MediaOrder;
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

    // Get hooks into doctrine.
    $doctrine = $this->getDoctrine();
    $em = $doctrine->getManager();

    // Check if slide exists, to update, else create new slide.
    if ($post['id']) {
      // Load current slide.
      $slide = $doctrine->getRepository('IndholdskanalenMainBundle:Slide')
        ->findOneById($post['id']);

      if (!$slide) {
        $response = new Response();
        $response->setStatusCode(404);

        return $response;
      }
    }
    else {
      // This is a new slide.
      $slide = new Slide();
    }

    // Update fields from post.
    if (isset($post['title'])) {
      $slide->setTitle($post['title']);
    }
    if (isset($post['orientation'])) {
      $slide->setOrientation($post['orientation']);
    }
    if (isset($post['template'])) {
      $slide->setTemplate($post['template']);
    }
    if (isset($post['created_at'])) {
      $slide->setCreatedAt($post['created_at']);
    }
    if (isset($post['options'])) {
      $slide->setOptions($post['options']);
    }
    if (isset($post['duration'])) {
      $slide->setDuration($post['duration']);
    }
    if (isset($post['published'])) {
      $slide->setPublished($post['published']);
    }
    if (isset($post['schedule_from'])) {
      $slide->setScheduleFrom($post['schedule_from']);
    }
    if (isset($post['schedule_to'])) {
      $slide->setScheduleTo($post['schedule_to']);
    }

    // Update user.
    $userEntity = $this->get('security.context')->getToken()->getUser();
    $slide->setUser($userEntity->getId());

    // Get channel ids.
    $postChannelIds = array();
    foreach($post['channels'] as $channel) {
      $postChannelIds[] = $channel['id'];
    }

    // Update channel orders.
    foreach($slide->getChannelSlideOrders() as $channelSlideOrder) {
      $channel = $channelSlideOrder->getChannel();

      if (!in_array($channel->getId(), $postChannelIds)) {
        $channelSlideOrder->getChannel()->removeChannelSlideOrder($channelSlideOrder);
        $channelSlideOrder->getSlide()->removeChannelSlideOrder($channelSlideOrder);

        $em->persist($channelSlideOrder->getChannel());
        $em->persist($channelSlideOrder->getSlide());

        $em->remove($channelSlideOrder);
        //$em->flush();
      }
    }

    // Save the entity.
    $em->persist($slide);
    //$em->flush();

    // Add to channels.
    foreach($post['channels'] as $channel) {
      $channel = $doctrine->getRepository('IndholdskanalenMainBundle:Channel')->findOneById($channel['id']);

      // Check if ChannelSlideOrder already exists, if not create it.
      $channelSlideOrder = $doctrine->getRepository('IndholdskanalenMainBundle:ChannelSlideOrder')->findOneBy(
        array(
          'channel' => $channel,
          'slide' => $slide,
        )
      );
      if (!$channelSlideOrder) {
        // Find the next sort order index for the given channel.
        $index = 0;
        $channelLargestSortOrder = $doctrine->getRepository('IndholdskanalenMainBundle:ChannelSlideOrder')->findOneBy(
          array(
            'channel' => $channel
          ),
          array(
            'sortOrder' => 'DESC'
          )
        );
        if ($channelLargestSortOrder) {
          $index = $channelLargestSortOrder->getSortOrder();
        }

        // Create new ChannelSlideOrder.
        $channelSlideOrder = new ChannelSlideOrder();
        $channelSlideOrder->setChannel($channel);
        $channelSlideOrder->setSlide($slide);
        $channelSlideOrder->setSortOrder($index + 1);

        // Save the ChannelSlideOrder.
        $em->persist($channelSlideOrder);
        //$em->flush();
      }
    }

    // Get channel ids.
    $postMediaIds = array();
    foreach($post['media'] as $media) {
      $postMediaIds[] = $media['id'];
    }

    // Update media orders.
    foreach($slide->getMediaOrders() as $mediaOrder) {
      $media = $mediaOrder->getMedia();

      if (!in_array($media->getId(), $postMediaIds)) {
        $mediaOrder->getMedia()->removeMediaOrder($mediaOrder);
        $mediaOrder->getSlide()->removeMediaOrder($mediaOrder);

        $em->persist($mediaOrder->getMedia());
        $em->persist($mediaOrder->getSlide());

        $em->remove($mediaOrder);
        //$em->flush();
      }
    }

    // Save the entity.
    $em->persist($slide);
    //$em->flush();

    // Add to media.
    foreach($post['media'] as $media) {
      $media = $doctrine->getRepository('IndholdskanalenMainBundle:Media')->findOneById($media['id']);

      // Check if ChannelSlideOrder already exists, if not create it.
      $mediaOrder = $doctrine->getRepository('IndholdskanalenMainBundle:MediaOrder')->findOneBy(
        array(
          'media' => $media,
          'slide' => $slide,
        )
      );
      if (!$mediaOrder) {
        // Find the next sort order index for the given channel.
        $index = 0;
        $mediaLargestSortOrder = $doctrine->getRepository('IndholdskanalenMainBundle:MediaOrder')->findOneBy(
          array(
            'media' => $media
          ),
          array(
            'sortOrder' => 'DESC'
          )
        );
        if ($mediaLargestSortOrder) {
          $index = $mediaLargestSortOrder->getSortOrder();
        }

        // Create new ChannelSlideOrder.
        $mediaOrder = new MediaOrder();
        $mediaOrder->setMedia($media);
        $mediaOrder->setSlide($slide);
        $mediaOrder->setSortOrder($index + 1);

        // Save the ChannelSlideOrder.
        $em->persist($mediaOrder);
        //$em->flush();
      }
    }

    // Save the slide.
    $em->persist($slide);

    // Persist to database.
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

      // Add channels.
      $channels = array();
      foreach($slide->getChannelSlideOrders() as $channelSlideOrder) {
        $channels[] = json_decode($serializer->serialize($channelSlideOrder->getChannel(), 'json'));
      }

      // Add channels.
      $media = array();
      foreach($slide->getMediaOrders() as $mediaOrder) {
        $media[] = json_decode($serializer->serialize($mediaOrder->getMedia(), 'json'));
      }
/*
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
*/
      // Create json content.
      $jsonContent = $serializer->serialize($slide, 'json');

      // Attach extra fields.
      $ob = json_decode($jsonContent);
      //$ob->imageUrls = $imageUrls;
      //$ob->videoUrls = $videoUrls;
      $ob->media = $media;
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
