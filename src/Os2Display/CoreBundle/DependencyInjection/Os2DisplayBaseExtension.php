<?php

namespace Os2Display\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class Os2DisplayBaseExtension extends Extension {

  protected $dir = __DIR__;

  /**
   * {@inheritdoc}
   */
  public function load(array $configs, ContainerBuilder $container) {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $loader = new Loader\YamlFileLoader($container,
      new FileLocator($this->dir . '/../Resources/config'));
    $loader->load('services.yml');

    // Get angular configuration.
    $angularYmlFile = $this->dir . '/../Resources/config/angular.yml';
    if (file_exists($angularYmlFile)) {
      $angular = Yaml::parse(file_get_contents($angularYmlFile));

      // Extend registered angular assets.
      $assets = $container->hasParameter('external_assets') ? $container->getParameter('external_assets') : [];
      if (array_key_exists('assets',
          $angular) && is_array($angular['assets'])) {
        foreach ($angular['assets'] as $key => $module) {
          if (!in_array($key, $assets)) {
            $assets[$key] = $module;
          }
        }
      }
      $container->setParameter('external_assets', $assets);

      // Extend registered angular modules.
      $modules = $container->hasParameter('external_modules') ? $container->getParameter('external_modules') : [];
      if (array_key_exists('modules',
          $angular) && is_array($angular['modules'])) {
        foreach ($angular['modules'] as $key => $module) {
          if (!in_array($key, $modules)) {
            $modules[$key] = $module;
          }
        }
      }
      $container->setParameter('external_modules', $modules);

      // Extend registered angular apps.
      $apps = $container->hasParameter('external_apps') ? $container->getParameter('external_apps') : [];
      if (array_key_exists('apps', $angular) && is_array($angular['apps'])) {
        foreach ($angular['apps'] as $key => $app) {
          if (!in_array($key, $apps)) {
            $apps[$key] = $app;
          }
        }
      }
      $container->setParameter('external_apps', $apps);

      // Extend registered angular bootstrap.
      $bootstrap = $container->hasParameter('external_bootstrap') ? $container->getParameter('external_bootstrap') : [];
      if (array_key_exists('bootstrap',
          $angular) && is_array($angular['bootstrap'])) {
        foreach ($angular['bootstrap'] as $key => $module) {
          if (!in_array($key, $bootstrap)) {
            $bootstrap[$key] = $module;
          }
        }
      }
      $container->setParameter('external_bootstrap', $bootstrap);
    }
  }
}
