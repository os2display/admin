<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;

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
	  $user = $this->get('security.context')->getToken()->getUser();

	  $serializer = $this->get('jms_serializer');

	  $response = new Response();
	  $response->headers->set('Content-Type', 'application/json');
	  $json_content = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('api')));
	  $response->setContent($json_content);

	  return $response;
  }
}
