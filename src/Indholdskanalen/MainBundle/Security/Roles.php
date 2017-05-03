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
	const ROLE_ADMIN = 'ROLE_ADMIN';
	const ROLE_USER = 'ROLE_USER';
	const ROLE_SONATA_ADMIN = 'ROLE_SONATA_ADMIN';
	const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
	const ROLE_ALLOWED_TO_SWITCH = 'ROLE_ALLOWED_TO_SWITCH';
	const ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT = 'ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT';
}
