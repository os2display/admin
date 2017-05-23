<?php
/**
 * @file
 * Contains IndholdskanalenMainBundle.
 */

namespace Indholdskanalen\MainBundle;

use Indholdskanalen\MainBundle\Filter\GroupingFilter;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class IndholdskanalenMainBundle
 * @package Indholdskanalen\MainBundle
 */
class IndholdskanalenMainBundle extends Bundle {
  public function boot() {
    // Enable filter and inject container.
    // @see http://stackoverflow.com/a/14650403
    if (php_sapi_name() !== 'cli') {
      $em = $this->container->get('doctrine.orm.default_entity_manager');
      $conf = $em->getConfiguration();
      $conf->addFilter('filter_grouping', GroupingFilter::class);
      $em->getFilters()->enable('filter_grouping')->setContainer($this->container);
    }
  }
}
