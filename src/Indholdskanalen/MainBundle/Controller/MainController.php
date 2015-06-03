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
    // Add paths to css files for activated templates.
    $templates = array();
    $slideTemplates = $this->container->get('indholdskanalen.template_service')->getEnabledSlideTemplates();
    foreach ($slideTemplates as $template) {
      $templates[] = $template->getPathCss();
    }
    $screenTemplates = $this->container->get('indholdskanalen.template_service')->getEnabledScreenTemplates();
    foreach ($screenTemplates as $template) {
      $templates[] = $template->getPathCss();
    }

    return $this->render('IndholdskanalenMainBundle:Main:index.html.twig', array(
      'templates' => $templates,
    ));
  }
}
