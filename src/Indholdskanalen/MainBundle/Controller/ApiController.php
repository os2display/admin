<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Slide;


/**
 * @Route("/api")
 */
class ApiController extends Controller {
  /**
   * Get a list of all slides.
   *
   * @Route("/slides")
   *
   * @Return
   */
  public function SlidesGetAction(Request $request) {
    // Slide entities
    $slide_entities = $this->getDoctrine()->getRepository('MainBundle:Slide')
      ->findAll();

    // Build our slide array.
    $slides = array();
    foreach ($slide_entities as $slide) {
      $slides[] = array(
        'id' => $slide->getId(),
        'title' => $slide->getTitle(),
        'orientation' => $slide->getOrientation(),
        'template' => $slide->getTemplate(),
        'created' => $slide->getCreated(),
        'options' => unserialize($slide->getOptions()),
      );
    }

    $response = new Response(json_encode($slides));
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  /**
   * Save a (new) slide.
   *
   * @Route("/slide/save")
   *
   * @Return
   */
  public function SlideSaveAction(Request $request) {
    // Get posted slide information from the request.
    $post = json_decode($request->getContent());

    if ($post->id) {
      // Load current slide.
      $slide = $this->getDoctrine()->getRepository('MainBundle:Slide')
        ->findOneById($post->id);
    }
    else {
      // This is a new slide.
      $slide = new Slide();
    }

    // Update fields.
    $slide->setTitle($post->title);
    $slide->setOrientation($post->orientation);
    $slide->setCreated($post->created);
    $slide->setOptions(serialize($post->options));

    // Save the entity.
    $em = $this->getDoctrine()->getManager();
    $em->persist($slide);
    $em->flush();

    // Send response back to client.
    $response = new Response(json_encode(array('status' => TRUE)));
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }
}
