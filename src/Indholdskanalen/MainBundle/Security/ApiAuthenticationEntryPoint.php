<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 28/08/14
 * Time: 16:23
 */

namespace Indholdskanalen\MainBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;


class ApiAuthenticationEntryPoint implements AuthenticationEntryPointInterface {

  /**
   * Starts the authentication scheme.
   *
   * @param Request $request The request that resulted in an AuthenticationException
   * @param AuthenticationException $authException The exception that started the authentication process
   *
   * @return Response
   */
  public function start(Request $request, AuthenticationException $authException = null)
  {
    $array = array('success' => false);
    $response = new Response(json_encode($array), 401);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }
}