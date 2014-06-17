<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/twig")
 */
class TwigController extends Controller {
  /**
   * @Route("/{name}")
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function indexAction($name) {
    return $this->render('IndholdskanalenMainBundle:Main:' . $name);
  }
}
