<?php

namespace Indholdskanalen\MainBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/zencoder")
 */
class ZencoderController extends Controller {
  /**
   * Handle callback from Zencoder.
   *
   * @Route("/callback")
   * @Method("POST")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function callbackAction(Request $request) {
    $logger = $this->get('monolog.logger.zencoder');

    $status = TRUE;

    // Get posted channel information from the request.
    $json = $request->getContent();
    $post = json_decode($json);


    // Find the correct media.
    if (!isset($post->job->pass_through)) {
      $logger->error('Zencoder job called without any value in $post->job->pass_through.');
      $status = FALSE;
    }

    // Log that data have been return form zencoder.
    $logger->info($json);

    if ($status) {
      $em = $this->getDoctrine()->getManager();
      $job = new Job('ik:zencoder', array($json));
      $em->persist($job);
      $em->flush($job);
    }

    $response = new Response(json_encode(array($status)));
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }
}
