<?php

namespace Os2Display\CoreBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Os2Display\CoreBundle\Entity\Group;
use Os2Display\CoreBundle\Entity\User;
use Os2Display\CoreBundle\Security\EditVoter;
use Os2Display\CoreBundle\Security\Roles;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class SecurityManager {
  /**
   * @var \Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface
   */
  protected $tokenStorage;

  /**
   * @var \Doctrine\ORM\EntityManagerInterface
   */
  protected $manager;

  /**
   * @var \Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface
   */
  protected $decisionManager;

  /**
   * @var \Symfony\Component\Security\Core\Role\RoleHierarchyInterface
   */
  protected $roleHierarchy;

  public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $manager, AccessDecisionManagerInterface $decisionManager, RoleHierarchyInterface $roleHierarchy) {
    $this->tokenStorage = $tokenStorage;
    $this->manager = $manager;
    $this->decisionManager = $decisionManager;
    $this->roleHierarchy = $roleHierarchy;
  }

  public function decide($attributes, $object = null) {
    if (!is_array($attributes)) {
      $attributes = [$attributes];
    }

    if ($object instanceof Group) {
      $attribute = $attributes[0];
      switch ($attribute) {
        case 'can_add_user':
          return $this->canAddUserToGroup($object);
        case 'can_add_channel':
          return $this->canAddChannelToGroup($object);
        case 'can_add_slide':
          return $this->canAddSlideToGroup($object);
        case 'can_add_screen':
          return $this->canAddScreenToGroup($object);
      }
    }

    $token = $this->tokenStorage->getToken();

    return $this->decisionManager->decide($token, $attributes, $object);
  }

  protected function canAddUserToGroup(Group $group) {
    return $this->decide(EditVoter::UPDATE, $group);
  }

  protected function canAddChannelToGroup(Group $group) {
    return $this->decide(EditVoter::READ, $group);
  }

  protected function canAddSlideToGroup(Group $group) {
    return $this->decide(EditVoter::READ, $group);
  }

  protected function canAddScreenToGroup(Group $group) {
    return $this->decide(EditVoter::READ, $group);
  }

  public function getReachableRoles(User $user) {
    $userRoles = array_map(function ($role) {
      return new Role($role);
    }, $user->getRoles(FALSE));

    $roles = $this->roleHierarchy->getReachableRoles($userRoles);
    $roleNames = array_map(function (Role $role) {
      return $role->getRole();
    }, $roles);

    return array_unique(array_intersect($roleNames, Roles::getRoleNames()));
  }

  public function hasRole(User $user, $role) {
    return in_array($role, $this->getReachableRoles($user));
  }

  public function getUser() {
    return $this->tokenStorage->getToken()->getUser();
  }

  /**
   * Decide if current user can assign roles to a user.
   *
   * @param array $roleNames
   * @return bool
   */
  public function canAssignRoles(array $roleNames) {
    // Only super admin can assign super admin role.
    if (in_array(Roles::ROLE_SUPER_ADMIN, $roleNames) && !$this->decide(Roles::ROLE_SUPER_ADMIN)) {
      return FALSE;
    }

    // Only admin can assign admin role.
    if (in_array(Roles::ROLE_ADMIN, $roleNames) && !$this->decide(Roles::ROLE_ADMIN)) {
      return FALSE;
    }

    return $this->decide(Roles::ROLE_USER_ADMIN);
  }
}
