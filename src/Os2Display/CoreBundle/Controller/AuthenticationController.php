<?php

namespace Os2Display\CoreBundle\Controller;

use Os2Display\CoreBundle\Entity\SharingIndex;
use Os2Display\CoreBundle\Events\SharingServiceEvent;
use Os2Display\CoreBundle\Entity\Channel;
use Os2Display\CoreBundle\Entity\ChannelSlideOrder;
use Os2Display\CoreBundle\Events\SharingServiceEvents;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use JMS\Serializer\SerializationContext;

use Symfony\Component\HttpFoundation\Session\Session;


/**
 * @Route("/api/auth")
 */
class AuthenticationController extends Controller {
  /**
   * Get the authorization token
   *
   * @param $prefix
   *   The name of the endpoint to authenticate against.
   * @param $reauth
   *   Whether or not to delete the token before authentication.
   *   'reauth' if true, anything else is false
   *
   * @Route("/{prefix}/{reauth}")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function searchAuthGetAction($prefix, $reauth = '') {
    $response = new Response();
    $authenticationService = $this->container->get('os2display.authentication_service');

    $res = $authenticationService->getAuthentication($prefix, $reauth === 'reauth');

    $response->setStatusCode(200);
    $response->setContent(json_encode($res));

    return $response;
  }
}
