<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/mockups")
 */
class MockupsController extends Controller {
  /**
   * @Route("/{name}")
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function indexAction($name) {
    return $this->render('IndholdskanalenMainBundle:Mockups:' . $name);
  }
}
