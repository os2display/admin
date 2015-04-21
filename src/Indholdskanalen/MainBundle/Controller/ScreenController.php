<?php
/**
 * @file
 * Screen controller.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\Entity\ChannelScreenRegion;
use Proxies\__CG__\Indholdskanalen\MainBundle\Entity\SharedChannel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Entity\Screen;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    } while ($screen != NULL);

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
  public function screenSaveAction(Request $request) {
    // Get posted screen information from the request.
    $post = json_decode($request->getContent());

    // Get the entity manager.
    $em = $this->getDoctrine()->getManager();

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
    if (isset($post->description)) {
      $screen->setDescription($post->description);
    }
    $screen->setModifiedAt(time());

    // Set an activation code and empty token for new screens.
    if ($screen->getActivationCode() == NULL) {
      $screen->setActivationCode($this->getNewActivationCode());
      $screen->setToken('');
    }

    // Change the template if it is set.
    if (isset($post->template)) {
      $template = $this->getDoctrine()
        ->getRepository('IndholdskanalenMainBundle:ScreenTemplate')
        ->findOneById($post->template->id);

      if ($template) {
        $screen->setTemplate($template);
      }
    }

    // Set selected channels.
    if (isset($post->channel_screen_regions)) {
      $channelRepository = $this->getDoctrine()
        ->getRepository('IndholdskanalenMainBundle:Channel');
      $sharedChannelRepository = $this->getDoctrine()
        ->getRepository('IndholdskanalenMainBundle:SharedChannel');

      // Gather channel screen region ids.
      $ids = array();
      foreach ($post->channel_screen_regions as $channelScreenRegion) {
        if (isset($channelScreenRegion->id)) {
          $ids[] = $channelScreenRegion->id;
        }
      }

      // Remove ChannelScreenRegions no longer present in Screen.
      foreach ($screen->getChannelScreenRegions() as $channelScreenRegion) {
        if (!in_array($channelScreenRegion->getId(), $ids)) {
          $screen->removeChannelScreenRegion($channelScreenRegion);
        }
      }

      $sharingService = $this->get('indholdskanalen.sharing_service');

      // Add new ChannelScreenRegions.
      foreach ($post->channel_screen_regions as $channelScreenRegion) {
        if (!isset($channelScreenRegion->id)) {
          // If ChannelScreenRegion is with Channel
          if (isset($channelScreenRegion->channel)) {
            // Get channel.
            $channel = $channelRepository->findOneById($channelScreenRegion->channel->id);

            // If the channel exists, create new ChannelScreenRegion.
            if ($channel) {
              $newChannelScreenRegion = new ChannelScreenRegion();
              $newChannelScreenRegion->setChannel($channel);
              $newChannelScreenRegion->setRegion($channelScreenRegion->region);
              $newChannelScreenRegion->setScreen($screen);
              $newChannelScreenRegion->setSortOrder(1);

              $em->persist($newChannelScreenRegion);
            }
          }
          // If ChannelScreenRegion is with SharedChannel
          else {
            if (isset($channelScreenRegion->shared_channel)) {
              // Get shared channel.
              $sharedChannel = $sharedChannelRepository->findOneByUniqueId($channelScreenRegion->shared_channel->unique_id);

              // Get channel from sharing service.
              $result = $sharingService->getChannelFromIndex($channelScreenRegion->shared_channel->unique_id, $channelScreenRegion->shared_channel->index);

              if ($result['status'] !== 200) {
                throw new NotFoundHttpException();
              }

              $channelFromSharing = json_decode($result['content']);

              // No hits founds, or too many.
              if (!$channelFromSharing || $channelFromSharing->hits > 1 || $channelFromSharing->hits < 1) {
                throw new NotFoundHttpException();
              }

              // Encode channel as json.
              $channelFromSharing = $channelFromSharing->results[0];

              if (!$sharedChannel) {
                $sharedChannel = new SharedChannel();
                $sharedChannel->setCreatedAt(time());
                $sharedChannel->setUniqueId($channelScreenRegion->shared_channel->unique_id);
                $sharedChannel->setIndex($channelScreenRegion->shared_channel->index);
              }

              $sharedChannel->setContent(json_encode($channelFromSharing));
              $sharedChannel->setModifiedAt(time());
              $em->persist($sharedChannel);

              // If the channel exists, create new ChannelScreenRegion.
              if ($sharedChannel) {
                $newChannelScreenRegion = new ChannelScreenRegion();
                $newChannelScreenRegion->setSharedChannel($sharedChannel);
                $newChannelScreenRegion->setRegion($channelScreenRegion->region);
                $newChannelScreenRegion->setScreen($screen);
                $newChannelScreenRegion->setSortOrder(1);

                $em->persist($newChannelScreenRegion);
              }
            }
          }
        }
      }
    }

    // If this is an update of a screen, push update to middleware.
    if ($screen->getId() !== null) {
      $middlewareService = $this->get('indholdskanalen.middleware.communication');
      $middlewareService->pushScreenUpdate($screen);
    }

    // Save the entity.
    $em->persist($screen);
    $em->flush();

    // Send the json response back to client.
    $response = new Response($screen->getId());
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
  public function screenGetAction($id) {
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
  public function screenGetPostAction() {
    $request = Request::createFromGlobals();
    $body = json_decode($request->getContent());

    // Test for valid request parameters.
    if (!isset($body->id)) {
      return new Response('', 403);
    }

    // Get the screen entity with the given token.
    $screen = $this->getDoctrine()
      ->getRepository('IndholdskanalenMainBundle:Screen')
      ->findOneById($body->id);

    // Test for valid screen.
    if (!isset($screen)) {
      return new Response('', 404);
    }

    $serializer = $this->get('jms_serializer');

    // Generate the response.
    $response_data = array(
      'id' => $screen->getId(),
      'title' => $screen->getTitle(),
      'options' => $screen->getOptions(),
      'template' => json_decode($serializer->serialize($screen->getTemplate(), 'json', SerializationContext::create()
        ->setGroups(array('middleware'))))
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
      return new Response('', 403);
    }

    // Get the screen entity på activationCode.
    $screen = $this->getDoctrine()
      ->getRepository('IndholdskanalenMainBundle:Screen')
      ->findOneByActivationCode($body->activationCode);

    // Test for valid screen.
    if (!isset($screen)) {
      return new Response('', 403);
    }

    // Set token in screen and persist the screen to the db.
    $manager = $this->getDoctrine()->getManager();
    $manager->persist($screen);
    $manager->flush();

    $serializer = $this->get('jms_serializer');

    // Generate the response.
    return new Response(json_encode(array(
      'id' => $screen->getId(),
      'title' => $screen->getTitle(),
      'options' => $screen->getOptions(),
      'template' => json_decode($serializer->serialize($screen->getTemplate(), 'json', SerializationContext::create()
        ->setGroups(array('middleware'))))
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
  public function screenDeleteAction($id) {
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
