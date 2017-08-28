<?php

namespace Os2Display\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class Os2DisplayCoreExtension extends Os2DisplayBaseExtension {

  /**
   * {@inheritdoc}
   */
  public function load(array $configs, ContainerBuilder $container) {
    $this->dir = __DIR__;

    parent::load($configs, $container);
  }
}
