<?php
/**
 * @file
 * Screen controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Screen;
use JMS\Serializer\SerializationContext;

/**
 * ScreenController.
 *
 * @Route("/api/screen")
 */
class ScreenController extends Controller {
  /**
   * Generates a new unique activation code in the interval between 100000000 and 999999999.
   *
   * @return int
   */
  protected function getNewActivationCode() {
    do {
      // Pick a random activation code between 100000000 and 999999999.
      $code = rand(100000000, 999999999);

      // Test if the activation code already exists in the db.
      $screen = $this->getDoctrine()
        ->getRepository('IndholdskanalenMainBundle:Screen')
        ->findByActivationCode($code);
    }
    while ($screen != NULL);

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
      $screen = $this->getDoctrine()
        ->getRepository('IndholdskanalenMainBundle:Screen')
        ->findOneById($post->id);

      // Throw error if screen does not exist.
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

      // Set creator.
      $userEntity = $this->get('security.context')->getToken()->getUser();
      $screen->setUser($userEntity->getId());
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
    if (isset($post->description)) {
      $screen->setDescription($post->description);
    }
    $screen->setModifiedAt(time());

    // Set an activation code and empty token for new screens.
    if ($screen->getActivationCode() == NULL) {
      $screen->setActivationCode($this->getNewActivationCode());
      $screen->setToken("");
    }

    // Change the template if it is set.
    if (isset($post->template)) {
      $template = $this->getDoctrine()->getRepository('IndholdskanalenMainBundle:ScreenTemplate')->findOneById($post->template->id);

      if ($template) {
        $screen->setTemplate($template);
      }
    }

    // Save the entity.
    $em = $this->getDoctrine()->getManager();
    $em->persist($screen);
    $em->flush();

    // Send the json response back to client.
    $response = new Response();
    $response->setStatusCode(200);

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
    $screen = $this->getDoctrine()
      ->getRepository('IndholdskanalenMainBundle:Screen')
      ->findOneById($id);

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    if ($screen) {
      $serializer = $this->get('jms_serializer');
      $response->headers->set('Content-Type', 'application/json');
      $jsonContent = $serializer->serialize($screen, 'json', SerializationContext::create()
          ->setGroups(array('api'))
          ->enableMaxDepthChecks());
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
    if (!isset($body->id)) {
      return new Response("", 403);
    }

    // Get the screen entity with the given token.
    $screen = $this->getDoctrine()
      ->getRepository('IndholdskanalenMainBundle:Screen')
      ->findOneById($body->id);

    // Test for valid screen.
    if (!isset($screen)) {
      return new Response("", 404);
    }

    // Generate the response.
    $response_data = array(
      'id' => $screen->getId(),
      'title' => $screen->getTitle(),
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
  public function screenActivateAction(Request $request) {
    // Get request body as array.
    $body = json_decode($request->getContent());

    // Test for valid request parameters.
    if (!isset($body->activationCode)) {
      return new Response("", 403);
    }

    // Get the screen entity på activationCode.
    $screen = $this->getDoctrine()
      ->getRepository('IndholdskanalenMainBundle:Screen')
      ->findOneByActivationCode($body->activationCode);

    // Test for valid screen.
    if (!isset($screen)) {
      return new Response("", 403);
    }

    // Set token in screen and persist the screen to the db.
    $manager = $this->getDoctrine()->getManager();
    $manager->persist($screen);
    $manager->flush();

    // Generate the response.
    return new Response(json_encode(array(
      "id" => $screen->getId(),
      "title" => $screen->getTitle(),
    )), 200);
  }

  /**
   * Delete screen.
   *
   * @Route("/{id}")
   * @Method("DELETE")
   *
   * @param int $id
   *   Slide id of the slide to delete.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ScreenDeleteAction($id) {
    $screen = $this->getDoctrine()
      ->getRepository('IndholdskanalenMainBundle:Screen')
      ->findOneById($id);

    // Create response.
    $response = new Response();

    if ($screen) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($screen);
      $em->flush();

      // Element deleted.
      $response->setStatusCode(200);
    }
    else {
      // Not found.
      $response->setStatusCode(404);
    }

    return $response;
  }
}
