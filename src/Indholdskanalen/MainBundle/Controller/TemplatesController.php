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
use JMS\Serializer\SerializationContext;

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
    $templates = array();
    $templatesDirectory = $this->container->getParameter("templates_directory");
    $serverAddress = $this->container->getParameter("absolute_path_to_server");

    // Iterate through templates directory (configurable).
    if ($handle = opendir($templatesDirectory)) {
      while (false !== ($entry = readdir($handle))) {
        if (is_dir($templatesDirectory . $entry) && $entry !== '.' && $entry !== '..') {
          // Read config.json for template
          $str = file_get_contents($templatesDirectory . $entry . '/' . $entry . ".json");
          $obj = json_decode($str);

          $obj->icon = $serverAddress . "/" . $templatesDirectory . $entry . '/' . $obj->icon;

          $obj->paths->live = $serverAddress . "/" . $templatesDirectory . $entry . '/' . $obj->paths->live;
          $obj->paths->edit = $serverAddress . "/" . $templatesDirectory . $entry . '/' . $obj->paths->edit;
          $obj->paths->preview = $serverAddress . "/" . $templatesDirectory . $entry . '/' . $obj->paths->preview;

          $templates[] = $obj;
        }
      }

      closedir($handle);
    }

    // Create response.
	  $response = new Response();
	  $response->headers->set('Content-Type', 'application/json');
    $response->setContent(json_encode($templates));

    return $response;
  }
}
