<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Screen;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/api/screen")
 */
class ScreenController extends Controller {
  /**
   * Generates a new unique activation code in the interval between 100000 and 999999.
   *
   * @return int
   */
  protected function getNewActivationCode()
  {
    do {
      // Pick a random activation code between 100000000 and 999999999.
      $code = rand(100000000, 999999999);

      // Test if the activation code already exists in the db.
      $screen = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Screen')->findByActivationCode($code);
    } while ($screen != null);

    return $code;
  }

  /**
   * Save a (new) screen.
   *
   * @Route("")
   * @Method("POST")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreenSaveAction(Request $request) {
    // Get posted screen information from the request.
    $post = json_decode($request->getContent());

    if ($post->id) {
      // Load current slide.
      $screen = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Screen')
        ->findOneById($post->id);

      if (!$screen) {
        $response = new Response();
        $response->setStatusCode(404);

        return $response;
      }
    }
    else {
      // This is a new screen.
      $screen = new Screen();
      $screen->setCreatedAt(time());
    }

    // Update fields.
    if (isset($post->title)) {
      $screen->setTitle($post->title);
    }
    if (isset($post->orientation)) {
      $screen->setOrientation($post->orientation);
    }
    if (isset($post->width)) {
      $screen->setWidth($post->width);
    }
    if (isset($post->height)) {
      $screen->setHeight($post->height);
    }

    // Set an activation code and empty token for new screens.
    if ($screen->getActivationCode() == null) {
      $screen->setActivationCode($this->getNewActivationCode());
      $screen->setToken("");
    }

    // Get channel ids.
    $postChannelIds = array();
    foreach($post->channels as $channel) {
      $postChannelIds[] = $channel->id;
    }

    // Remove channels.
    foreach($screen->getChannels() as $channel) {
      if (!in_array($channel->getId(), $postChannelIds)) {
        $screen->removeChannel($channel);
      }
    }

    // Add channels.
    foreach($post->channels as $channel) {
      $channel = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Channel')
        ->findOneById($channel->id);
      if ($channel) {
        if (!$screen->getChannels()->contains($channel)) {
          $screen->addChannel($channel);
        }
      }
    }

    // Save the entity.
    $em = $this->getDoctrine()->getManager();
    $em->persist($screen);
    $em->flush();

    // Create the response data.
    $responseData = array(
      "id" => $screen->getId(),
      "title" => $screen->getTitle(),
      "orientation" => $screen->getOrientation(),
      "created_at" => $screen->getCreatedAt(),
      "width" => $screen->getWidth(),
      "height" => $screen->getHeight(),
    );

    // Send the json response back to client.
    $response = new Response(json_encode($responseData));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }


  /**
   * Get screen with ID.
   *
   * @Route("/{id}")
   * @Method("GET")
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreenGetAction($id) {
    $screen = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Screen')->findOneById($id);

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($screen) {
      $serializer = $this->get('jms_serializer');
      $response->headers->set('Content-Type', 'application/json');
      $jsonContent = $serializer->serialize($screen, 'json', SerializationContext::create()->setGroups(array('api')));
      $response->setContent($jsonContent);
    }
    else {
      $response->setContent(json_encode(array()));
    }

    return $response;
  }

  /**
   * Get screen with token
   *
   * NB! This function is used by the Middleware. Do not change unless you change the middleware as well.
   *
   * @Route("/get")
   * @Method("POST")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreenGetPostAction() {
    $request = Request::createFromGlobals();
    $body = json_decode($request->getContent());

    // Test for valid request parameters.
    if (!isset($body->token)) {
      return new Response("", 403);
    }

    // Get the screen entity with the given token.
    $screen = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Screen')->findOneByToken($body->token);

    // Test for valid screen.
    if (!isset($screen)) {
      return new Response("", 404);
    }

    // Set group id of screen.
    $groups = array();
    $groups[] = "group" . $screen->getId();

    // Generate the response.
    $response_data = array(
      'statusCode' => 200,
      'id' => $screen->getId(),
      'name' => $screen->getTitle(),
      'groups' => $groups,
    );

    // Return the json response.
    return new Response(json_encode($response_data), 200);
  }

  /**
   * Handler for the screenActivate action.
   *
   * NB! This function is used by the Middleware. Do not change unless you change the middleware as well.
   *
   * @Route("/activate")
   * @Method("POST")
   *
   * @param $request
   *
   * @return Response
   */
  public function screenActivateAction(Request $request)
  {
    // Get request body as array.
    $body = json_decode($request->getContent());

    // Test for valid request parameters.
    if (!isset($body->token) || !isset($body->activationCode)) {
      return new Response("", 403);
    }

    // Get the screen entity på activationCode.
    $screen = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:Screen')->findOneByActivationCode($body->activationCode);

    // Test for valid screen.
    if (!isset($screen)) {
      return new Response("", 403);
    }

    // Set token in screen and persist the screen to the db.
    $screen->setToken($body->token);
    $manager = $this->getDoctrine()->getManager();
    $manager->persist($screen);
    $manager->flush();

    // Generate the response.
    return new Response("", 200);
  }
}
