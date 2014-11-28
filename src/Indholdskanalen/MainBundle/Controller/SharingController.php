<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indholdskanalen\MainBundle\Controller;

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



    $response = new Response();
    $response->setStatusCode(200);
    return $response;
  }
}
