<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Application\Sonata\MediaBundle\Entity\Media;

/**
 * @Route("/media")
 */
class MediaController extends Controller {
  /**
   * Mange file upload.
   *
   * @Route("/upload")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function MediaUploadAction(Request $request) {
    foreach ($request->files as $file) {
      $media = new Media;

      $media->setName($file->getClientOriginalName());
      $media->setBinaryContent($file->getPathname());
      $media->setContext('default');
      $media->setProviderName('sonata.media.provider.image');

      $mediaManager = $this->get("sonata.media.manager.media");

      $mediaManager->save($media);
    }

    $response = new Response(json_encode(array()));
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  /**
   * Sends all uploaded media
   *
   * @Route("/list")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function MediaListAction(Request $request) {
    $results = $this->getDoctrine()->getManager()->createQuery('SELECT m FROM ApplicationSonataMediaBundle:Media m')
      ->getResult();

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
}
