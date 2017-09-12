<?php

namespace Os2Display\CoreBundle\Security;

/**
 * Class Roles
 *
 * A helper class to help using role names in code.
 *
 * @package Os2Display\CoreBundle\Security
 */
class Roles {
  const ROLE_USER = 'ROLE_USER';
  const ROLE_ADMIN = 'ROLE_ADMIN';
  const ROLE_GROUP_ADMIN = 'ROLE_GROUP_ADMIN';
  const ROLE_USER_ADMIN = 'ROLE_USER_ADMIN';
  const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

  public static function getRoleNames() {
    $class = new \ReflectionClass(static::class);

    return $class->getConstants();
  }

}
