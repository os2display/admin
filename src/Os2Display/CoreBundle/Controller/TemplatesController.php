<?php

namespace Os2Display\CoreBundle\Controller;

use Os2Display\CoreBundle\Entity\MediaOrder;
use Os2Display\CoreBundle\Entity\ChannelSlideOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Os2Display\CoreBundle\Services\TemplateService;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/api/templates")
 */
class TemplatesController extends Controller {
  /**
   * Get available slide templates.
   *
   * @Route("/slides/enabled")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function templatesGetSlidesAction() {
    $templateService = $this->container->get('os2display.template_service');
    $templates = $templateService->getEnabledSlideTemplates();
    $serializer = $this->container->get('jms_serializer');

    // Create response.
    $response = new JsonResponse();
    $response->setContent($serializer->serialize($templates, 'json', SerializationContext::create()->setGroups(array('api'))));

    return $response;
  }

  /**
   * Get available screen templates.
   *
   * @Route("/screens/enabled")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function templatesGetScreensAction() {
    $templateService = $this->container->get('os2display.template_service');
    $templates = $templateService->getEnabledScreenTemplates();
    $serializer = $this->container->get('jms_serializer');

    // Create response.
    $response = new JsonResponse();
    $response->setContent($serializer->serialize($templates, 'json', SerializationContext::create()->setGroups(array('api'))));

    return $response;
  }

  /**
   * Get all screen templates.
   *
   * @Route("/screens/all")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function templatesGetAllScreens() {
    $templateService = $this->container->get('os2display.template_service');
    $templates = $templateService->getAllScreenTemplates();
    $serializer = $this->container->get('jms_serializer');

    // Create response.
    $response = new JsonResponse();
    $response->setContent($serializer->serialize($templates, 'json', SerializationContext::create()->setGroups(array('api'))));

    return $response;
  }

  /**
   * Get all slide templates.
   *
   * @Route("/slides/all")
   * @Method("GET")
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function templatesGetAllSlides() {
    $templateService = $this->container->get('os2display.template_service');
    $templates = $templateService->getAllSlideTemplates();
    $serializer = $this->container->get('jms_serializer');

    // Create response.
    $response = new JsonResponse();
    $response->setContent($serializer->serialize($templates, 'json', SerializationContext::create()->setGroups(array('api'))));

    return $response;
  }

  /**
   * Get all slide templates.
   *
   * @Route("/save/enabled")
   * @Method("POST")
   *
   * @param Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function templatesPostEnabled(Request $request) {
    $body = json_decode($request->getContent());

    $templateService = $this->container->get('os2display.template_service');
    $templateService->enableScreenTemplates($body->screens);
    $templateService->enableSlideTemplates($body->slides);

    return new JsonResponse();
  }
}
