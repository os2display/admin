<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/")
 */
class MainController extends Controller {
  /**
   * @Route("/")
   */
  public function indexAction() {
    return $this->render('IndholdskanalenMainBundle:Main:index.html.twig');
  }
}
