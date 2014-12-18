<?php

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\Entity\SharingIndex;
use Indholdskanalen\MainBundle\Events\SharingServiceEvent;
use Indholdskanalen\MainBundle\Entity\Channel;
use Indholdskanalen\MainBundle\Entity\ChannelSlideOrder;
use Indholdskanalen\MainBundle\Events\SharingServiceEvents;

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
  public function SearchAuthGetAction($prefix, $reauth = '') {
    $response = new Response();
    $authenticationService = $this->container->get('indholdskanalen.authentication_service');

    $token = $authenticationService->getAuthentication($prefix, $reauth === 'reauth');

    // Create response.
    $response->setStatusCode(200);
    $response->setContent($token);

    return $response;
  }
}
