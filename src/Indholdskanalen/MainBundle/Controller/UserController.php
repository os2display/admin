<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Application\Sonata\MediaBundle\Entity\Media;

/**
 * @Route("/api/user")
 */
class UserController extends Controller {
  /**
   * Sends current user.
   *
   * @Route("")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function UserAction() {
    $userEntity = $this->get('security.context')->getToken()->getUser();
    $user = array(
      'id' => $userEntity->getId(),
      'username' => $userEntity->getUsername(),
      'email' => $userEntity->getEmail(),
    );

    $response = new Response(json_encode($user));
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }
}
