<?php

namespace Indholdskanalen\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;

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

    // Get current user.
    $user = $this->getUser();
    $user->buildRoleGroups();
    $user = $this->get('os2display.api_data')->setApiData($user);
    $user = $this->get('serializer')->serialize($user, 'json', SerializationContext::create()
      ->setGroups(array('api'))
      ->enableMaxDepthChecks());

    return $this->render('IndholdskanalenMainBundle:Main:index.html.twig', array(
      'templates' => $templates,
      'user' => $user
    ));
  }
}
