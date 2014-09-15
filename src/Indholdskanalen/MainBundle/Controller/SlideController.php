<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    if ($post['id']) {
      // Load current slide.
      $slide = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Slide')
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

    // Save the entity.
    $em = $this->getDoctrine()->getManager();
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

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($slide) {
      // Get handle to media.
      $sonataMedia = $this->getDoctrine()->getRepository('ApplicationSonataMediaBundle:Media');

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

            // @TODO: add correct urls to mp4 and ogg
            //$videoUrls[$videoId] = $content->urls;
          }
        }
      }

      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($slide, 'json');

      // Add media urls to slide
      $ob = json_decode($jsonContent);
      $ob->imageUrls = $imageUrls;
      $ob->videoUrls = $videoUrls;
      $jsonContent = json_encode($ob);

      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }
}
