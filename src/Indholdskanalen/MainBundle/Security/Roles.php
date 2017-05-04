<?php

namespace Indholdskanalen\MainBundle\Security;

/**
 * Class Roles
 *
 * A helper class to help using role names in code.
 *
 * @package Indholdskanalen\MainBundle\Security
 */
class Roles {
  const ROLE_USER = 'ROLE_USER';
  const ROLE_ADMIN = 'ROLE_ADMIN';
  const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

  public static function getRoleNames() {
    $class = new \ReflectionClass(static::class);

    return $class->getConstants();
  }

}
