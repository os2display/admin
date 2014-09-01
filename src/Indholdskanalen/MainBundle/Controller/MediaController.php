<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Application\Sonata\MediaBundle\Entity\Media;

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
  public function MediaUploadAction(Request $request) {
    $title = $request->request->get('title');

    $uploadedItems = array();

    foreach ($request->files as $file) {
      $media = new Media;

      if (isset($title) && $title !== '') {
        $media->setName($title);
      } else {
        $path_parts = pathinfo($file->getClientOriginalName());
        $media->setName($path_parts['filename']);
      }

      switch ($file->getMimeType()) {
        case 'video/mp4':
          $media->setProviderName('sonata.media.provider.zencoder');
          break;

        case 'image/png':
          $media->setProviderName('sonata.media.provider.image');
          break;

        default:
          $media->setProviderName('sonata.media.provider.image');
      }

      $media->setBinaryContent($file->getPathname());
      $media->setContext('default');

      $mediaManager = $this->get("sonata.media.manager.media");

      $mediaManager->save($media);

      $uploadedItems[] = $media->getId();
    }

    // @TODO: send status codes
    $response = new Response(json_encode($uploadedItems));
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  /**
   * Sends all uploaded media.
   *
   * @Route("")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function MediaListAction() {
    $em = $this->getDoctrine()->getManager();
    $qb = $em->createQueryBuilder();

    $qb->select('m')
      ->from('ApplicationSonataMediaBundle:Media', 'm')
      ->orderBy('m.updatedAt', 'DESC');

    $query = $qb->getQuery();
    $results = $query->getResult();

    $response = new Response();

    $serializer = $this->get('jms_serializer');
    $jsonContent = $serializer->serialize($results, 'json');

    $response->setContent($jsonContent);
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

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
  public function MediaGetAction($id) {
    $media = $this->getDoctrine()->getRepository('ApplicationSonataMediaBundle:Media')
      ->findOneById($id);

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    if ($media) {
      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($media, 'json');

      $response->setContent($jsonContent);
    } else {
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
  public function MediaDeleteAction($id) {
    $em = $this->getDoctrine()->getManager();
    $media = $this->getDoctrine()->getRepository('ApplicationSonataMediaBundle:Media')
      ->findOneById($id);

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    if($media) {
      $em->remove(($media));
      $em->flush();
      $response->setContent(json_encode(array()));
    } else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }


}
