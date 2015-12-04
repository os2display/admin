<?php
/**
 * @file
 * Contains the calls the screen can make to the administration.
 */

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ClientApiController
 *
 * @Route("/client")
 *
 * @package Indholdskanalen\MainBundle\Controller
 */
class ClientApiController extends Controller {
  /**
   * Load templates.
   *
   * @Route("/keys/{id}")
   * @Method("GET")
   */
  public function getKey($id) {
    $keys = $this->container->getParameter('keys');

    if (array_key_exists($id, $keys)) {
      $response =  new JsonResponse(array($id => $keys[$id]));
      $response->headers->set('Access-Control-Allow-Origin', '*');
      return $response;
    }
    else {
      throw new NotFoundHttpException();
    }
  }
}
