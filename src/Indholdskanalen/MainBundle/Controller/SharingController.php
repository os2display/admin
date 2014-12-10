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
   * Get the authentication token.
   *
   * @Route("/authenticate")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SharingAuthenticateGetAction() {
    $sharingService = $this->container->get('indholdskanalen.sharing_service');
    $token = $sharingService->sharingAuthenticate();

    $response = new Response();
    $response->setContent($token);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

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
    $sharingService = $this->container->get('indholdskanalen.sharing_service');
    $doctrine = $this->container->get('doctrine');
    $em = $doctrine->getManager();

    $channelFromSharing = json_encode(json_decode($sharingService->getChannelFromIndex($id, $index)));

    if ($channelFromSharing) {
      $sharedChannel = $doctrine->getRepository('IndholdskanalenMainBundle:SharedChannel')->findOneByUniqueId($id);

      if (!$sharedChannel) {
        $sharedChannel = new SharedChannel();
        $sharedChannel->setUniqueId($id);
        $sharedChannel->setIndex($index);
        $sharedChannel->setCreatedAt(time());
        $em->persist($sharedChannel);
      }
      $sharedChannel->setContent($channelFromSharing);
      $sharedChannel->setModifiedAt(time());

      $em->flush();
    }

    $response = new Response();
    $response->setContent(json_encode($channelFromSharing));
    $response->headers->set('Content-Type', 'application/json');
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
