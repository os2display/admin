<?php

namespace Os2Display\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @TODO: Create common parent class for Os2Display Extensions.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class Os2DisplayAdminExtension extends Extension {
  /**
   * {@inheritdoc}
   */
  public function load(array $configs, ContainerBuilder $container) {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.yml');

    // Get angular configuration.
    $angular = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/angular.yml'));

    // Extend registered angular modules.
    $modules = $container->hasParameter('external_modules') ? $container->getParameter('external_modules') : [];
    if (array_key_exists('modules', $angular) && is_array($angular['modules'])) {
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
  }
}
