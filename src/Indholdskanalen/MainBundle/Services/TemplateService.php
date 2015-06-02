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
use Indholdskanalen\MainBundle\Entity\SlideTemplate;

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
   * Gets all enabled slide templates.
   *
   * @return array
   *   Slide Templates.
   */
  public function getEnabledSlideTemplates() {
    return $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:SlideTemplate')->findByEnabled(TRUE);
  }

  /**
   * Gets all enabled screen templates.
   *
   * @return array
   *   array of screen templates.
   */
  public function getEnabledScreenTemplates() {
    return $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:ScreenTemplate')->findByEnabled(TRUE);
  }

  /**
   * Gets all slide templates from the 'templates_slides_directory' defined in parameters.yml.
   *
   * @return array
   *   Slide Templates.
   */
  public function getAllSlideTemplates() {
    return $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:SlideTemplate')->findAll();
  }

  /**
   * Gets all screen templates from the 'templates_screens_directory' defined in parameters.yml.
   *
   * @return array
   *   array of screen templates.
   */
  public function getAllScreenTemplates() {
    return $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:ScreenTemplate')->findAll();
  }

  /**
   * Enable screen templates.
   * @param $enabledTemplates
   */
  public function enableScreenTemplates($enabledTemplates) {
    $doctrine = $this->container->get('doctrine');
    $templateRepository = $doctrine->getRepository('IndholdskanalenMainBundle:ScreenTemplate');
    $templates = $templateRepository->findAll();
    $entityManager = $doctrine->getManager();

    foreach ($templates as $template) {
      $en = FALSE;

      foreach ($enabledTemplates as $enabled) {
        if ($enabled->id === $template->getId()) {
          $en = TRUE;
          break;
        }
      }

      $template->setEnabled($en);
    }

    $entityManager->flush();
  }

  /**
   * Enable slide templates.
   * @param $enabledTemplates
   */
  public function enableSlideTemplates($enabledTemplates) {
    $doctrine = $this->container->get('doctrine');
    $templateRepository = $doctrine->getRepository('IndholdskanalenMainBundle:SlideTemplate');
    $templates = $templateRepository->findAll();
    $entityManager = $doctrine->getManager();

    foreach ($templates as $template) {
      $en = FALSE;

      foreach ($enabledTemplates as $enabled) {
        if ($enabled->id === $template->getId()) {
          $en = TRUE;
          break;
        }
      }

      $template->setEnabled($en);
    }

    $entityManager->flush();
  }

  /**
   * Load the slide templates from the disc to the database.
   */
  private function loadSlideTemplates() {
    // Get database hooks.
    $doctrine = $this->container->get('doctrine');
    $templateRepository = $doctrine->getRepository('IndholdskanalenMainBundle:SlideTemplate');
    $entityManager = $doctrine->getManager();

    // Get parameters.
    $path = $this->container->get('kernel')->getRootDir() . '/../web/templates/slides';
    $serverAddress = $this->container->getParameter('absolute_path_to_server') . '/templates/slides';

    $it = new \RecursiveDirectoryIterator($path);
    foreach (new \RecursiveIteratorIterator($it) as $file)  {
      $ext = pathinfo($file, PATHINFO_EXTENSION);

      $dir = explode('/../web/templates/slides', pathinfo($file, PATHINFO_DIRNAME));
      $dir = $dir[1];

      if ($ext === 'json') {
        // Read config.json for template
        $str = file_get_contents($file);
        $obj = json_decode($str);

        // Try to load the template.
        $template = $templateRepository->findOneById($obj->id);

        // Check if the template was loaded, if not create a new template entity.
        if (!$template) {
          $template = new SlideTemplate();
          $template->setId($obj->id);
          $template->setName($obj->name);
          $template->setEnabled(false);
        }

        // Set the template values on the entity.
        $template->setPathIcon($serverAddress . $dir . '/' . $obj->icon);
        $template->setPathLive($serverAddress . $dir . '/' . $obj->paths->live);
        $template->setPathEdit($serverAddress . $dir . '/' . $obj->paths->edit);
        $template->setPathCss($serverAddress . $dir . '/' . $obj->paths->css);
        $template->setPathPreview($serverAddress . $dir . '/' . $obj->paths->preview);
        $template->setPath($serverAddress . $dir . '/');
        $template->setOrientation($obj->orientation);
        $template->setEmptyOptions($obj->empty_options);
        $template->setIdealDimensions($obj->ideal_dimensions);
        $template->setMediaType($obj->media_type);
        if (isset($obj->slide_type)) {
          $template->setSlideType($obj->slide_type);
        }

        // Ensure that the entity is managed.
        $entityManager->persist($template);
      }
    }

    // Make it stick in the database.
    $entityManager->flush();
  }

  /**
   * Load the screen templates from the disc to the database.
   */
  private function loadScreenTemplates() {
    // Get database hooks.
    $doctrine = $this->container->get('doctrine');
    $templateRepository = $doctrine->getRepository('IndholdskanalenMainBundle:ScreenTemplate');
    $entityManager = $doctrine->getManager();

    // Get parameters.
    $path = $this->container->get('kernel')->getRootDir() . '/../web/templates/screens';
    $serverAddress = $this->container->getParameter('absolute_path_to_server') . '/templates/screens';

    $it = new \RecursiveDirectoryIterator($path);
    foreach (new \RecursiveIteratorIterator($it) as $file)  {
      $ext = pathinfo($file, PATHINFO_EXTENSION);

      $dir = explode('/../web/templates/screens', pathinfo($file, PATHINFO_DIRNAME));
      $dir = $dir[1];

      if ($ext === 'json') {
        // Read config.json for template
        $str = file_get_contents($file);
        $obj = json_decode($str);

        // Try to load the template.
        $template = $templateRepository->findOneById($obj->id);

        // Check if the template was loaded, if not create a new template entity.
        if (!$template) {
          $template = new ScreenTemplate();
          $template->setId($obj->id);
          $template->setName($obj->name);
          $template->setEnabled(false);
        }

        // Set the template values on the entity.
        $template->setPathIcon($serverAddress . $dir . '/' . $obj->icon);
        $template->setPathLive($serverAddress . $dir . '/' . $obj->paths->live);
        $template->setPathEdit($serverAddress . $dir . '/' . $obj->paths->edit);
        $template->setPathCss($serverAddress . $dir . '/' . $obj->paths->css);
        $template->setPath($serverAddress . '/' . $dir);
        $template->setOrientation($obj->orientation);

        // Ensure that the entity is managed.
        $entityManager->persist($template);
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

  /**
   * Load templates into database.
   */
  public function loadTemplates() {
    $this->loadScreenTemplates();
    $this->loadSlideTemplates();
  }
}
