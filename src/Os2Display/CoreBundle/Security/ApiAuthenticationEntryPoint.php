<?php
/**
 * @file
 * Contains the ApiAuthenticationEntryPoint.
 */

namespace Os2Display\CoreBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Class ApiAuthenticationEntryPoint
 * @package Os2Display\CoreBundle\Security
 */
class ApiAuthenticationEntryPoint implements AuthenticationEntryPointInterface {
  /**
   * Starts the authentication scheme.
   *
   * @param Request $request The request that resulted in an AuthenticationException
   * @param null|AuthenticationException $authException The exception that started the authentication process
   *
   * @return Response
   */
  public function start(Request $request, AuthenticationException $authException = NULL) {
    $array = array('success' => FALSE);
    $response = new Response(json_encode($array), 401);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }
}
