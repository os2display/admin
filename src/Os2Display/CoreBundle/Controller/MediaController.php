<?php

namespace Os2Display\CoreBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Os2Display\MediaBundle\Entity\Media;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/api/media")
 */
class MediaController extends Controller {
  /**
   * Manage file upload.
   *
   * @Route("")
   * @Method("POST")
   *
   * @param $request
   *   The Request object.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function mediaUploadAction(Request $request) {
    $title = $request->request->get('title');

    $uploadedItems = array();

    foreach ($request->files as $file) {
      $media = new Media();

      if (isset($title) && $title !== '') {
        $media->setName($title);
      }
      else {
        $path_parts = pathinfo($file->getClientOriginalName());
        $media->setName($path_parts['filename']);
      }

      $mediaType = explode('/', $file->getMimeType());
      $mediaType = $mediaType[0];

      switch ($mediaType) {
        case 'video':
          $media->setProviderName('sonata.media.provider.zencoder');
          $media->setMediaType('video');
          break;

        case 'image':
          $media->setProviderName('sonata.media.provider.image');

          $isLogo = $request->request->get('logo');
          if (isset($isLogo) && $isLogo === 'true') {
            $media->setMediaType('logo');
          }
          else {
            $media->setMediaType('image');
          }

          break;

        default:
          $media->setProviderName('sonata.media.provider.image');
      }

      $media->setBinaryContent($file->getPathname());
      $media->setContext('default');

      // Set creator.
      $userEntity = $this->get('security.context')->getToken()->getUser();
      $media->setUser($userEntity->getId());

      $groups = json_decode($request->request->get('groups'));
      $groups = new ArrayCollection($groups ?: []);
      $media->setGroups($groups);

      $mediaManager = $this->get('sonata.media.manager.media');
      $mediaManager->save($media);

      $uploadedItems[] = $media->getId();
    }

    $response = new Response(json_encode($uploadedItems));
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  /**
   * Get all uploaded media.
   *
   * @Route("")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function mediaListAction() {
    $manager = $this->get('os2display.entity_manager');
    $results = $manager->findBy(Media::class, [], ['updatedAt' => Criteria::DESC]);

    $response = new Response();

    $serializer = $this->get('jms_serializer');
    $jsonContent = $serializer->serialize($results, 'json', SerializationContext::create()
        ->setGroups(array('api')));

    $response->setContent($jsonContent);
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  /**
   * Update media with ID.
   *
   * @Route("/{id}")
   * @Method("PUT")
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function mediaUpdateAction(Request $request, $id) {
    $serializer = $this->get('jms_serializer');

    $media = $this->getDoctrine()
      ->getRepository(Media::class)
      ->findOneById($id);

    $post = json_decode($request->getContent());

    // Add groups.
    $this->get('os2display.group_manager')->setGroups(isset($post->groups) ? $post->groups : [], $media);
    if (isset($post->name)) {
      $media->setName($post->name);
    }

    // Hack: Make the entity Dirty!
    $media->setMediaOrders(clone($media->getMediaOrders()));

    $mediaManager = $this->get('sonata.media.manager.media');
    $mediaManager->save($media);

    $jsonContent = $serializer->serialize($media, 'json', SerializationContext::create()
      ->setGroups(array('api')));

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    $response->setContent($jsonContent);
    return $response;
  }

  /**
   * Get media with ID.
   *
   * @Route("/{id}")
   * @Method("GET")
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function mediaGetAction($id) {
    $media = $this->getDoctrine()
      ->getRepository(Media::class)
      ->findOneById($id);

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    if ($media) {
      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($media, 'json', SerializationContext::create()
          ->setGroups(array('api')));

      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }

  /**
   * Delete media with ID.
   *
   * @Route("/{id}")
   * @Method("DELETE")
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function mediaDeleteAction($id) {
    $em = $this->getDoctrine()->getManager();
    $media = $this->getDoctrine()
      ->getRepository(Media::class)
      ->findOneById($id);

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    if ($media && $media->getMediaOrders()->isEmpty()) {
      $em->remove(($media));
      $em->flush();
      $response->setContent(json_encode(array()));
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }


}
