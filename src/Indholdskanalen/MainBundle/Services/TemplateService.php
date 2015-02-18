<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indholdskanalen\MainBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class TemplateService
 *
 * @package Indholdskanalen\MainBundle\Services
 */
class TemplateService extends ContainerAware {
  protected $slideTemplates;
  protected $screenTemplates;
  protected $container;

  /**
   * Constructor.
   *
   * @param Container $container
   *   The service container.
   */
  public function __construct(Container $container) {
    $this->container = $container;
  }

  /**
   * Gets all slide templates from the "templates_slides_directory" defined in parameters.yml.
   */
  public function getSlideTemplates() {
    if ($this->slideTemplates) {
      return $this->slideTemplates;
    }

    $this->slideTemplates = array();
    $enabledTemplates = $this->container->getParameter("templates_slides_enabled");

    $path = $this->container->get('kernel')->getRootDir() . '/../web/' . $this->container->getParameter("templates_slides_directory");
    $serverAddress = $this->container->getParameter("absolute_path_to_server") . "/" . $this->container->getParameter("templates_slides_directory");;

    // Iterate through templates directory (configurable).
    if ($handle = opendir($path)) {
      while (false !== ($entry = readdir($handle))) {
        if (is_dir($path . "/" . $entry) && $entry !== '.' && $entry !== '..') {
          if (!in_array($entry, $enabledTemplates)) {
            continue;
          }

          // Read config.json for template
          $str = file_get_contents($path . $entry . '/' . $entry . ".json");
          $obj = json_decode($str);

          $obj->icon = $serverAddress . $entry . '/' . $obj->icon;

          $obj->paths->live = $serverAddress . $entry . '/' . $obj->paths->live;
          $obj->paths->edit = $serverAddress . $entry . '/' . $obj->paths->edit;
          $obj->paths->preview = $serverAddress . $entry . '/' . $obj->paths->preview;
          $obj->paths->css = $serverAddress . $entry . '/' . $obj->paths->css;

          $this->slideTemplates[$entry] = $obj;
        }
      }

      closedir($handle);
    }

    return $this->slideTemplates;
  }

  /**
   * Gets all screen templates from the "templates_screens_directory" defined in parameters.yml.
   */
  public function getScreenTemplates() {
    if ($this->screenTemplates) {
      return $this->screenTemplates;
    }

    $this->screenTemplates = array();
    $enabledTemplates = $this->container->getParameter("templates_screens_enabled");

    $path = $this->container->get('kernel')->getRootDir() . '/../web/' . $this->container->getParameter("templates_screens_directory");
    $serverAddress = $this->container->getParameter("absolute_path_to_server") . "/" . $this->container->getParameter("templates_screens_directory");;

    // Iterate through templates directory (configurable).
    if ($handle = opendir($path)) {
      while (false !== ($entry = readdir($handle))) {
        if (is_dir($path . "/" . $entry) && $entry !== '.' && $entry !== '..') {
          if (!in_array($entry, $enabledTemplates)) {
            continue;
          }

          // Read config.json for template
          $str = file_get_contents($path . $entry . '/' . $entry . ".json");
          $obj = json_decode($str);

          $obj->icon = $serverAddress . $entry . '/' . $obj->icon;
          $obj->paths->live = $serverAddress . $entry . '/' . $obj->paths->live;
          $obj->paths->edit = $serverAddress . $entry . '/' . $obj->paths->edit;
          $obj->paths->preview = $serverAddress . $entry . '/' . $obj->paths->preview;
          $obj->paths->css = $serverAddress . $entry . '/' . $obj->paths->css;

          $this->screenTemplates[$entry] = $obj;
        }
      }

      closedir($handle);
    }

    return $this->screenTemplates;
  }
}
