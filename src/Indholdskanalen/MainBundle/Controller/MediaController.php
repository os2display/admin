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

    foreach ($request->files as $file) {
      $media = new Media;

      if (isset($title) && $title !== '') {
        $media->setName($title);
      } else {
        // @TODO: FIX BUG: This line fails.
        $media->setName($file->originalName);
      }
      $media->setBinaryContent($file->getPathname());
      $media->setContext('default');
      $media->setProviderName('sonata.media.provider.image');

      $mediaManager = $this->get("sonata.media.manager.media");

      $mediaManager->save($media);
    }

    // @TODO: send status codes
    $response = new Response(json_encode(array()));
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

    $items = array();
    foreach ($results as $media) {
      $provider = $this->container->get($media->getProviderName());

      $items[] = array(
        'id' => $media->getId(),
        'name' => $media->getName(),
        'url' => array(
          'landscape' => $provider->generatePublicUrl($media, 'default_landscape'),
          'portrait' => $provider->generatePublicUrl($media, 'default_portrait'),
        )
      );
    }

    $response = new Response(json_encode($items));
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
      $provider = $this->container->get($media->getProviderName());
      $data = array();
      $data['media'] = $media;
      $data['urls'] = array(
        'landscape' => $provider->generatePublicUrl($media, 'default_landscape'),
        'portrait' => $provider->generatePublicUrl($media, 'default_portrait'),
      );

      $serializer = $this->get('jms_serializer');
      $jsonContent = $serializer->serialize($data, 'json');

      $response->setContent($jsonContent);
    } else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }


}
