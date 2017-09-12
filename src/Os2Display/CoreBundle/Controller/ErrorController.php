<?php

namespace Os2Display\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/error")
 */
class ErrorController extends Controller {
  /**
   * Save error to the log.
   *
   * @Route("")
   * @Method("POST")
   *
   * @param Request $request
   *
   * @return Response
   */
  public function postError(Request $request) {
    $content = $request->getContent();
    $logger = $this->get('logger');
    $logger->error(urldecode($content));

    return new Response();
  }
}
