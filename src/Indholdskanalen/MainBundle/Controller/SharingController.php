<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\Entity\SharingIndex;
use Indholdskanalen\MainBundle\Entity\SharedChannel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/api/sharing")
 */
class SharingController extends Controller {
  /**
   * Get a shared channel with id from index
   *
   * @Route("/channel/{id}/{index}")
   * @Method("GET")
   *
   * @param int $id
   * @param string $index
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SharedChannelGetAction($id, $index) {
    $response = new Response();
    $sharingService = $this->container->get('indholdskanalen.sharing_service');
    $doctrine = $this->container->get('doctrine');
    $em = $doctrine->getManager();

    // Get channel from sharing service.
    $result = $sharingService->getChannelFromIndex($id, $index);

    if ($result['status'] !== 200) {
      $response = new Response();
      $response->setStatusCode($result['status']);
      return $response;
    }

    $channelFromSharing = json_decode($result['content']);

    // No hits founds, or too many.
    if (!$channelFromSharing) {
      $response->setStatusCode(500);
      $response->setContent("Error: Could not retrieve channel from sharing service.");
      return $response;
    }
    else if ($channelFromSharing->hits > 1) {
      $response->setStatusCode(500);
      $response->setContent("Error: More than one entry with that id found.");
      return $response;
    }
    else if ($channelFromSharing->hits < 1) {
      $response->setStatusCode(404);
      return $response;
    }

    // Encode channel as json.
    $channelFromSharing = $channelFromSharing->results[0];

    // Get shared channel entity with unique id.
    $sharedChannel = $doctrine->getRepository('IndholdskanalenMainBundle:SharedChannel')->findOneByUniqueId($id);

    // If the shared channel does not exist, create it.
    if (!$sharedChannel) {
      $sharedChannel = new SharedChannel();
      $sharedChannel->setUniqueId($id);
      $sharedChannel->setIndex($index);
      $sharedChannel->setCreatedAt(time());
      $em->persist($sharedChannel);
    }

    // Update content.
    $sharedChannel->setContent(json_encode($channelFromSharing));
    $sharedChannel->setModifiedAt(time());

    // Update database.
    $em->flush();

    // Serialize shared channel
    $serializer = $this->get('jms_serializer');
    $content = $serializer->serialize($sharedChannel, 'json', SerializationContext::create()->setGroups(array('api'))->enableMaxDepthChecks());

    // Send response.
    $response->setContent($content);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  /**
   * Save a shared channel.
   *
   * @Route("/channel")
   * @Method("POST")
   *
   * @param Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SharedChannelSaveAction(Request $request) {
    $post = json_decode($request->getContent());

    $doctrine = $this->getDoctrine();
    $em = $doctrine->getManager();
    $sharedChannelRepository = $doctrine->getRepository('IndholdskanalenMainBundle:SharedChannel');
    $screenRepository = $doctrine->getRepository('IndholdskanalenMainBundle:Screen');

    // Add new sharing indexes and enable all selected sharingIndexes
    $sharedChannel = $sharedChannelRepository->findOneByUniqueId($post->unique_id);

    if (!$sharedChannel) {
      $response = new Response();
      $response->setStatusCode(404);
      return $response;
    }

    // Get all sharing_indexes ids from POST.
    $post_screens_ids = array();
    foreach ($post->screens as $screen) {
      $post_screens_ids[] = $screen->id;
    }

    // Remove screens.
    foreach ($sharedChannel->getScreens() as $screen) {
      if (!in_array($screen->getId(), $post_screens_ids)) {
        $sharedChannel->removeScreen($screen);
      }
    }

    // Add screens.
    foreach ($post_screens_ids as $screenId) {
      $screen = $screenRepository->findOneById($screenId);
      if ($screen) {
        if (!$sharedChannel->getScreens()->contains($screen)) {
          $sharedChannel->addScreen($screen);
        }
      }
    }

    $em->flush();

    $response = new Response();
    $response->setStatusCode(200);
    return $response;
  }

  /**
   * Get a list of available sharing indexes.
   *
   * @Route("/available_indexes")
   * @Method("GET")
   *
   * @return Response
   */
  public function AvailableSharingIndexesGetAction() {
    $sharingService = $this->container->get('indholdskanalen.sharing_service');
    $availableSharingIndexes = $sharingService->getAvailableSharingIndexes();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    $response->setContent($availableSharingIndexes);

    return $response;
  }

  /**
   * Get a list of all sharing indexes.
   *
   * @Route("/indexes")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SharingIndexesGetAction() {
    $sharingService = $this->container->get('indholdskanalen.sharing_service');
    $sharingIndexes = $sharingService->getSharingIndexes();

    // Create response.
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');

    $serializer = $this->get('jms_serializer');
    $json_content = $serializer->serialize($sharingIndexes, 'json', SerializationContext::create()->setGroups(array('api'))->enableMaxDepthChecks());

    $response->setContent($json_content);

    return $response;
  }

  /**
   * Save a list of sharing indexes.
   *
   * @Route("/indexes")
   * @Method("POST")
   *
   * @param Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SharingIndexesSaveAction(Request $request) {
    $post = json_decode($request->getContent());

    $doctrine = $this->getDoctrine();
    $em = $doctrine->getManager();
    $sharingIndexRepository = $doctrine->getRepository('IndholdskanalenMainBundle:SharingIndex');

    // Disable all sharingIndexes
    $sharingIndexes = $sharingIndexRepository->findAll();
    foreach ($sharingIndexes as $sharingIndex) {
      $sharingIndex->setEnabled(false);
    }

    // Add new sharing indexes and enable all selected sharingIndexes
    foreach ($post as $postSharingIndex) {
      $sharingIndex = $sharingIndexRepository->findOneByIndex($postSharingIndex->index);

      if (!$sharingIndex) {
        $sharingIndex = new SharingIndex();
        $sharingIndex->setIndex($postSharingIndex->index);
        $sharingIndex->setName($postSharingIndex->name);
        $sharingIndex->setEnabled(true);

        $em->persist($sharingIndex);
      }
      else {
        $sharingIndex->setEnabled(true);
      }
    }

    $em->flush();

    $response = new Response();
    $response->setStatusCode(200);
    return $response;
  }
}
