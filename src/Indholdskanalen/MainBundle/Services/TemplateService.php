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
   *
   * @param array $enabledTemplates
   *   Templates object for the templates to enable.
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
   *
   * @param array $enabledTemplates
   *   Templates object for the templates to enable.
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
    $path = $this->container->get('kernel')->getRootDir() . '/../web/templates';
    $serverAddress = $this->container->getParameter('absolute_path_to_server') . '/templates';

    $it = new \RecursiveDirectoryIterator($path);
    foreach (new \RecursiveIteratorIterator($it) as $file)  {
      $ext = pathinfo($file, PATHINFO_EXTENSION);

      if ($ext === 'json') {
        // Get relative path.
        $dir = explode('/../web/templates', pathinfo($file, PATHINFO_DIRNAME));
        $dir = $dir[1];

        // Read config.json for template
        $str = file_get_contents($file);
        $config = json_decode($str);

        if ($config->type == 'slide') {
          // Try to load the template.
          $template = $templateRepository->findOneById($config->id);

          // Check if the template was loaded, if not create a new template entity.
          if (!$template) {
            $template = new SlideTemplate();
            $template->setId($config->id);
            $template->setEnabled(false);
          }
          $template->setName($config->name);

          // Set the template values on the entity. The css, live, edit and preview files need to be prefixed with their last
          // modified timestamp to ensure they are load by the screen clients.
          $template->setPathIcon($serverAddress . $dir . '/' . $config->icon);
          $template->setPathLive($this->buildFilePath($serverAddress, $path, $dir, $config->paths->live));
          $template->setPathEdit($this->buildFilePath($serverAddress, $path, $dir, $config->paths->edit));
          $template->setPathCss($this->buildFilePath($serverAddress, $path, $dir, $config->paths->css));
          $template->setPathPreview($this->buildFilePath($serverAddress, $path, $dir, $config->paths->preview));
          $template->setPath($serverAddress . $dir . '/');
          $template->setOrientation($config->orientation);
          $template->setEmptyOptions($config->empty_options);
          $template->setIdealDimensions($config->ideal_dimensions);
          $template->setMediaType($config->media_type);
          if (isset($config->tools)) {
            $template->setTools($config->tools);
          }
          if (isset($config->slide_type)) {
            $template->setSlideType($config->slide_type);
          }
          if (isset($config->paths->js)) {
            $template->setPathJs($serverAddress . $dir . '/' . $config->paths->js);
          }
          $template->setScriptId($config->script_id);

          // Ensure that the entity is managed.
          $entityManager->persist($template);
        }
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
    $path = $this->container->get('kernel')->getRootDir() . '/../web/templates';
    $serverAddress = $this->container->getParameter('absolute_path_to_server') . '/templates';

    $it = new \RecursiveDirectoryIterator($path);
    foreach (new \RecursiveIteratorIterator($it) as $file)  {
      $ext = pathinfo($file, PATHINFO_EXTENSION);

      $dir = explode('/../web/templates', pathinfo($file, PATHINFO_DIRNAME));
      $dir = $dir[1];

      if ($ext === 'json') {
        // Read config.json for template
        $str = file_get_contents($file);
        $config = json_decode($str);

        if ($config->type == 'screen') {
          // Try to load the template.
          $template = $templateRepository->findOneById($config->id);

          // Check if the template was loaded, if not create a new template entity.
          if (!$template) {
            $template = new ScreenTemplate();
            $template->setId($config->id);
            $template->setEnabled(FALSE);
          }
          $template->setName($config->name);

          // Set the template values on the entity. The css, live and edit files need to be prefixed with their last
          // modified timestamp to ensure they are load by the screen clients.
          $template->setPathIcon($serverAddress . $dir . '/' . $config->icon);
          $template->setPathLive($this->buildFilePath($serverAddress, $path, $dir, $config->paths->live));
          $template->setPathEdit($this->buildFilePath($serverAddress, $path, $dir, $config->paths->edit));
          $template->setPathCss($this->buildFilePath($serverAddress, $path, $dir, $config->paths->css));
          $template->setPath($serverAddress . '/' . $dir);
          $template->setOrientation($config->orientation);

          // Check if the template comes with any tools.
          $template->setTools(array());
          if (!empty($config->tools)) {
            // Ensure path is correct.
            foreach ($config->tools as &$tool) {
              $tool = '/templates/screens' . $dir . '/' . $tool;
            }

            // Add the tools to the template.
            $template->setTools((array) $config->tools);
          }

          // Ensure that the entity is managed.
          $entityManager->persist($template);
        }
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
   * Build template file paths.
   *
   * @param $serverAddress
   *   The http address of this server.
   * @param $path
   *   Base file path on the server.
   * @param $dir
   *   Relative "web" directory on the server-
   * @param $file
   *   The filename.
   *
   * @return string
   *   URL to the file with it's modified timestamp prefixed.
   */
  private function buildFilePath($serverAddress, $path, $dir, $file) {
    return $serverAddress . $dir . '/' . $file . '?' . filemtime($path . '/' . $dir . '/' . $file);
  }

  /**
   * Load templates into database.
   */
  public function loadTemplates() {
    $this->loadScreenTemplates();
    $this->loadSlideTemplates();
  }
}
