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

    // Get angular modules and apps from other bundles.
    $externalModules = $this->container->hasParameter('external_modules') ? $this->container->getParameter('external_modules') : [];
    $externalApps = $this->container->hasParameter('external_apps') ? $this->container->getParameter('external_apps') : [];

    return $this->render('IndholdskanalenMainBundle:Main:index.html.twig', array(
      'apps' => array_merge($this->container->getParameter('apps'), $externalApps),
      'modules' => array_merge($this->container->getParameter('modules'), $externalModules),
      'templates' => $templates,
      'user' => $user
    ));
  }
}
