<?php

namespace Indholdskanalen\MainBundle\Security;

/**
 * Class GroupRoles
 *
 * A helper class to help using group role names in code.
 *
 * @package Indholdskanalen\MainBundle\Security
 */
class GroupRoles {
  const ROLE_GROUP_ROLE_USER = 'ROLE_GROUP_ROLE_USER';
  const ROLE_GROUP_ROLE_ADMIN = 'ROLE_GROUP_ROLE_ADMIN';

  public static function getRoleNames() {
    $class = new \ReflectionClass(static::class);

    return $class->getConstants();
  }

}
