<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indholdskanalen\MainBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class TemplateService
 *
 * @package Indholdskanalen\MainBundle\Services
 */
class TemplateService extends ContainerAware
{
  /**
   * Gets all templates from the "templates_directory" defined in parameters.yml.
   */
  public function getTemplates() {
    $templates = array();
    $path = $this->container->get('kernel')->getRootDir() . '/../web/' . $this->container->getParameter("templates_directory");
    $serverAddress = $this->container->getParameter("absolute_path_to_server") . "/" . $this->container->getParameter("templates_directory");;

    // Iterate through templates directory (configurable).
    if ($handle = opendir($path)) {
      while (false !== ($entry = readdir($handle))) {
        if (is_dir($path . "/" . $entry) && $entry !== '.' && $entry !== '..') {
          // Read config.json for template
          $str = file_get_contents($path . $entry . '/' . $entry . ".json");
          $obj = json_decode($str);

          $obj->icon = $serverAddress . $entry . '/' . $obj->icon;

          $obj->paths->live = $serverAddress . $entry . '/' . $obj->paths->live;
          $obj->paths->edit = $serverAddress . $entry . '/' . $obj->paths->edit;
          $obj->paths->preview = $serverAddress . $entry . '/' . $obj->paths->preview;

          $templates[$entry] = $obj;
        }
      }

      closedir($handle);
    }

    return $templates;
  }
}
