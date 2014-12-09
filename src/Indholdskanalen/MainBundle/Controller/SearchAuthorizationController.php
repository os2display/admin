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
 * @Route("/api/search/auth")
 */
class SearchAuthorizationController extends Controller {
  private function authenticate() {
    $apikey = $this->container->getParameter('search_apikey');
    $search_host = $this->container->getParameter('search_host');

    // Build query.
    $ch = curl_init($search_host . "/authenticate");

    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

    $jsonContent = json_encode(
      array(
        'apikey' => $apikey
      )
    );

    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonContent);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));

    // Receive server response.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close connection.
    curl_close($ch);

    if ($http_status === 200) {
      return json_decode($content)->token;
    }
    else {
      return false;
    }
  }

  /**
   * Get search authorization token
   *
   * @Route("")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SearchAuthGetAction() {
    $session = new Session();
    $token = null;

    // If the token is set return it.
    if ($session->has('search_token')) {
      $token = $session->get('search_token');
    }
    else {
      $token = $this->authenticate();
      if ($token) {
        $session->set('search_token', $token);
      }
    }

    // Create response.
    $response = new Response();
    $response->setContent(
      json_encode(
        array('token' => $token)
      )
    );

    return $response;
  }

  /**
   * Get search authorization token
   *
   * @Route("/reauthorize")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function SearchReauthorizeAction() {
    $session = new Session();
    $session->remove('search_token');

    $token = $this->authenticate();
    if ($token) {
      $session->set('search_token', $token);
    }

    // Create response.
    $response = new Response();
    $response->setContent(
      json_encode(
        array('token' => $token)
      )
    );


    return $response;
  }
}