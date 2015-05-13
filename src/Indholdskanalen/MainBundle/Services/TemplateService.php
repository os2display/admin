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
use Indholdskanalen\MainBundle\Entity\ScreenTemplate;

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
   * Gets all slide templates from the 'templates_slides_directory' defined in parameters.yml.
   *
   * @return array
   *   array of slideTemplates.
   */
  public function getSlideTemplates() {
    if ($this->slideTemplates) {
      return $this->slideTemplates;
    }

    $this->slideTemplates = array();
    $enabledTemplates = $this->container->getParameter('templates_slides_enabled');

    $path = $this->container->get('kernel')->getRootDir() . '/../web/' . $this->container->getParameter('templates_slides_directory');
    $serverAddress = $this->container->getParameter('absolute_path_to_server') . '/' . $this->container->getParameter('templates_slides_directory');

    // Iterate through templates directory (configurable).
    if ($handle = opendir($path)) {
      while (false !== ($entry = readdir($handle))) {
        if (is_dir($path . '/' . $entry) && $entry !== '.' && $entry !== '..') {
          if (!in_array($entry, $enabledTemplates)) {
            continue;
          }

          // Read config.json for template
          $str = file_get_contents($path . $entry . '/' . $entry . '.json');
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
   * Gets all screen templates from the 'templates_screens_directory' defined in parameters.yml.
   */
  public function getScreenTemplates() {
    return $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:ScreenTemplate')->findByEnabled(TRUE);
  }

  /**
   * Load templates into database.
   */
  public function loadTemplates() {
    // Get database hooks.
    $doctrine = $this->container->get('doctrine');
    $templateRepository = $doctrine->getRepository('IndholdskanalenMainBundle:ScreenTemplate');
    $entityManager = $doctrine->getManager();

    $existingTemplates = $templateRepository->findAll();

    // Get parameters.
    $enabledTemplates = $this->container->getParameter('templates_screens_enabled');
    $path = $this->container->get('kernel')
        ->getRootDir() . '/../web/' . $this->container->getParameter('templates_screens_directory');
    $serverAddress = $this->container->getParameter('absolute_path_to_server') . '/' . $this->container->getParameter('templates_screens_directory');

    // Loop over enable templates from the configuration.
    foreach($enabledTemplates as $entry) {
      // Read .json for template
      $str = file_get_contents($path . $entry . '/' . $entry . '.json');
      $obj = json_decode($str);

      // Try to load the template.
      $template = $templateRepository->findOneById($obj->id);

      // Check if the template was loaded, if not create a new template entity.
      if (!$template) {
        $template = new ScreenTemplate();
        $template->setId($obj->id);
        $template->setName($obj->name);
      }

      // Set the template values on the entity.
      $template->setPathIcon($serverAddress . $entry . '/' . $obj->icon);
      $template->setPathLive($serverAddress . $entry . '/' . $obj->paths->live);
      $template->setPathEdit($serverAddress . $entry . '/' . $obj->paths->edit);
      $template->setPathCss($serverAddress . $entry . '/' . $obj->paths->css);
      $template->setPath($serverAddress . $entry . '/');
      $template->setOrientation($obj->orientation);

      $template->setEnabled(true);

      // Ensure that the entity is managed.
      $entityManager->persist($template);
    }

    // Remove templates that are not in parameters and the disk.
    foreach ($existingTemplates as $existingTemplate) {
      if (!in_array($existingTemplate->getId(), $enabledTemplates)) {
        $existingTemplate->setEnabled(false);
      }
    }

    // Make it stick in the database.
    $entityManager->flush();

    // Get all templates from the database, and push update to screens.
    $existingTemplates = $templateRepository->findAll();
    $middlewareService = $this->container->get('indholdskanalen.middleware.communication');
    foreach ($existingTemplates as $template) {
      foreach ($template->getScreens() as $screen) {
        $middlewareService->pushScreenUpdate($screen);
      }
    }
  }
}
