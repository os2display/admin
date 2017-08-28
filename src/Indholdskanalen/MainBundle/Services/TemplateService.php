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
use Indholdskanalen\MainBundle\Entity\ScreenTemplate;
use Indholdskanalen\MainBundle\Entity\SlideTemplate;

/**
 * Class TemplateService
 *
 * @package Indholdskanalen\MainBundle\Services
 */
class TemplateService {
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
    return $this->container->get('doctrine')
      ->getRepository('IndholdskanalenMainBundle:SlideTemplate')
      ->findByEnabled(TRUE);
  }

  /**
   * Gets all enabled screen templates.
   *
   * @return array
   *   array of screen templates.
   */
  public function getEnabledScreenTemplates() {
    return $this->container->get('doctrine')
      ->getRepository('IndholdskanalenMainBundle:ScreenTemplate')
      ->findByEnabled(TRUE);
  }

  /**
   * Gets all slide templates from the 'templates_slides_directory' defined in parameters.yml.
   *
   * @return array
   *   Slide Templates.
   */
  public function getAllSlideTemplates() {
    return $this->container->get('doctrine')
      ->getRepository('IndholdskanalenMainBundle:SlideTemplate')
      ->findAll();
  }

  /**
   * Gets all screen templates from the 'templates_screens_directory' defined in parameters.yml.
   *
   * @return array
   *   array of screen templates.
   */
  public function getAllScreenTemplates() {
    return $this->container->get('doctrine')
      ->getRepository('IndholdskanalenMainBundle:ScreenTemplate')
      ->findAll();
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
    return $serverAddress . '/' . $dir . '/' . $file . '?' . filemtime($path . '/' . $dir . '/' . $file);
  }

  /**
   * Find the templates in path.
   *
   * @param $path
   *
   * @return array
   */
  private function findTemplates($path) {
    $dir_iterator = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
    $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::SELF_FIRST);

    $screens = [];
    $slides = [];
    foreach ($iterator as $info) {
      $pathName = $info->getPathname();

      if (pathinfo($pathName, PATHINFO_EXTENSION) == 'json' && strpos($pathName, '/templates/') > -1) {
        if (strpos($pathName, '/slides/') > -1) {
          $slides[] = $pathName;
        }
        else if (strpos($pathName, '/screens/') > -1) {
          $screens[] = $pathName;
        }
      }
    }

    return [
      'screens' => $screens,
      'slides' => $slides,
    ];
  }

  private function loadScreenTemplate($file, $templateRepository, $entityManager, $dir, $serverAddress, $path) {
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
      $template->setPathIcon($this->buildFilePath($serverAddress, $path, $dir, $config->icon));
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
          $tool = $this->buildFilePath($serverAddress, $path, $dir, $tool);
        }

        // Add the tools to the template.
        $template->setTools((array) $config->tools);
      }

      // Ensure that the entity is managed.
      $entityManager->persist($template);
    }

    $entityManager->flush();
  }

  private function loadSlideTemplate($file, $templateRepository, $entityManager, $dir, $serverAddress, $path) {
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
        $template->setEnabled(FALSE);
      }
      $template->setName($config->name);

      // Set the template values on the entity. The css, live, edit and preview files need to be prefixed with their last
      // modified timestamp to ensure they are load by the screen clients.
      $template->setPathIcon($this->buildFilePath($serverAddress, $path, $dir, $config->icon));
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
        // Fix paths if defined to be relative.
        foreach ($config->tools as $tool) {
          if (isset($tool->path)) {
            $tool->path = $this->buildFilePath($serverAddress, $path, $dir, $tool->path);
          }
        }

        $template->setTools($config->tools);
      }
      if (isset($config->slide_type)) {
        $template->setSlideType($config->slide_type);
      }
      if (isset($config->paths->js)) {
        $template->setPathJs($serverAddress . '/' . $config->paths->js);
      }
      $template->setScriptId($config->script_id);

      // Ensure that the entity is managed.
      $entityManager->persist($template);
    }

    $entityManager->flush();
  }

  /**
   * Load templates into database.
   */
  public function loadTemplates() {
    // Get database hooks.
    $doctrine = $this->container->get('doctrine');
    $slideTemplateRepository = $doctrine->getRepository('IndholdskanalenMainBundle:SlideTemplate');
    $screenemplateRepository = $doctrine->getRepository('IndholdskanalenMainBundle:ScreenTemplate');
    $entityManager = $doctrine->getManager();

    // Get parameters.
    $path = $this->container->get('kernel')->getRootDir() . '/../web/';
    $serverAddress = $this->container->getParameter('absolute_path_to_server');

    // Locate templates in /web/bundles/
    $templates = $this->findTemplates($path . 'bundles/');

    foreach ($templates['slides'] as $config) {
      $dir = explode('/web/', pathinfo($config, PATHINFO_DIRNAME));
      $this->loadSlideTemplate($config, $slideTemplateRepository, $entityManager, $dir[1], $serverAddress, $path);
    }

    foreach ($templates['screens'] as $config) {
      $dir = explode('/web/', pathinfo($config, PATHINFO_DIRNAME));
      $this->loadScreenTemplate($config, $screenemplateRepository, $entityManager, $dir[1], $serverAddress, $path);
    }

    // Get all templates from the database, and push update to screens.
    $existingTemplates = $screenemplateRepository->findAll();
    $middlewareService = $this->container->get('indholdskanalen.middleware.communication');
    foreach ($existingTemplates as $template) {
      foreach ($template->getScreens() as $screen) {
        $middlewareService->pushScreenUpdate($screen);
      }
    }
  }
}
