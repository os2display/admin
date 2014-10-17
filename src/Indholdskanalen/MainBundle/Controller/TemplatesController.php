<?php

namespace Indholdskanalen\MainBundle\Controller;

use Indholdskanalen\MainBundle\Entity\MediaOrder;
use Indholdskanalen\MainBundle\Entity\ChannelSlideOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Indholdskanalen\MainBundle\Services\TemplateService;

/**
 * @Route("/api/templates")
 */
class TemplatesController extends Controller {
  /**
   * Get available templates.
   *
   * @Route("/")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function TemplatesGetAction() {
    $templateService = $this->container->get('indholdskanalen.template_service');
    $templates = $templateService->getTemplates();

    // Create response.
	  $response = new Response();
	  $response->headers->set('Content-Type', 'application/json');
    $response->setContent(json_encode($templates));

    return $response;
  }
}
