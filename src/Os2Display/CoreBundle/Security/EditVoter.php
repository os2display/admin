<?php

namespace Os2Display\CoreBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Os2Display\CoreBundle\Entity\Group;
use Os2Display\CoreBundle\Entity\GroupableEntity;
use Os2Display\CoreBundle\Entity\User;
use Os2Display\CoreBundle\Entity\UserGroup;
use Os2Display\CoreBundle\Services\SecurityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EditVoter extends Voter {
  protected $manager;
  protected $decisionManager;
  protected $securityManager;

  const CREATE = 'CREATE';
  const READ = 'READ';
  const UPDATE = 'UPDATE';
  const DELETE = 'DELETE';
  const READ_LIST = 'LIST';

  public function __construct(EntityManagerInterface $manager, AccessDecisionManagerInterface $decisionManager, SecurityManager $securityManager) {
    $this->manager = $manager;
    $this->decisionManager = $decisionManager;
    $this->securityManager = $securityManager;
  }

  protected function supports($attribute, $subject) {
    return in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE, self::READ_LIST]);
  }

  protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
    if ($this->decisionManager->decide($token, [Roles::ROLE_ADMIN])) {
      return TRUE;
    }

    $user = $token->getUser();
    if (!$user instanceof User) {
      // the user must be logged in; if not, deny access
      return FALSE;
    }

    switch ($attribute) {
      case self::CREATE:
        return $this->canCreate($subject, $token);

      case self::READ:
        return $this->canRead($subject, $token);

      case self::UPDATE:
        return $this->canUpdate($subject, $token);

      case self::DELETE:
        return $this->canDelete($subject, $token);

      case self::READ_LIST:
        return $this->canList($subject, $token);
    }

    return FALSE;
  }

  private function canCreate($type, TokenInterface $token) {
    switch ($type) {
      case Group::class:
        return $this->canCreateGroup($token);

      case User::class:
        return $this->canCreateUser($token);
    }

    return FALSE;
  }

  private function canRead($subject, TokenInterface $token) {
    if ($subject instanceof Group) {
      return $this->canReadGroup($subject, $token);
    }
    elseif ($subject instanceof User) {
      return $this->canReadUser($subject, $token);
    }
    elseif ($subject instanceof GroupableEntity) {
      return $this->canReadGroupable($subject, $token);
    }

    return FALSE;
  }

  private function canUpdate($subject, TokenInterface $token) {
    if ($subject instanceof Group) {
      return $this->canUpdateGroup($subject, $token);
    }
    elseif ($subject instanceof User) {
      return $this->canUpdateUser($subject, $token);
    }
    elseif ($subject instanceof GroupableEntity) {
      return $this->canUpdateGroupable($subject, $token);
    }

    return FALSE;
  }

  private function canDelete($subject, TokenInterface $token) {
    if ($subject instanceof Group) {
      return $this->canDeleteGroup($subject, $token);
    }
    elseif ($subject instanceof User) {
      return $this->canDeleteUser($subject, $token);
    }
    elseif ($subject instanceof GroupableEntity) {
      return $this->canDeleteGroupable($subject, $token);
    }

    return FALSE;
  }

  private function canList($type, TokenInterface $token) {
    switch ($type) {
      case Group::class:
      case 'group':
        return $this->canListGroup($token);
      case User::class:
      case 'user':
        return $this->canListUser($token);
    }

    return FALSE;
  }

  // ---------------------------------------------------------------------------
  // Group
  // ---------------------------------------------------------------------------

  private function canCreateGroup(TokenInterface $token) {
    return $this->decisionManager->decide($token, [Roles::ROLE_GROUP_ADMIN]);
  }

  private function canReadGroup(Group $group, TokenInterface $token) {
    if ($this->decisionManager->decide($token, [Roles::ROLE_GROUP_ADMIN])) {
      return TRUE;
    }

    $roles = $this->manager->getRepository(UserGroup::class)->findBy([
      'group' => $group,
      'user' => $token->getUser(),
    ]);

    return count($roles) > 0;
  }

  private function canUpdateGroup(Group $group, TokenInterface $token) {
    if ($this->decisionManager->decide($token, [Roles::ROLE_GROUP_ADMIN])) {
      return TRUE;
    }

    $roles = $this->manager->getRepository(UserGroup::class)->findBy([
      'group' => $group,
      'user' => $token->getUser(),
      'role' => GroupRoles::ROLE_GROUP_ROLE_ADMIN,
    ]);

    return count($roles) > 0;
  }

  private function canDeleteGroup(Group $group, TokenInterface $token) {
    return $this->canUpdateGroup($group, $token);
  }

  private function canListGroup(TokenInterface $token) {
    if ($this->decisionManager->decide($token, [Roles::ROLE_GROUP_ADMIN])) {
      return TRUE;
    }

    // A user can list groups if he is manager of a group.
    $items = $this->manager->getRepository(UserGroup::class)->findBy([
      'user' => $token->getUser(),
      'role' => GroupRoles::ROLE_GROUP_ROLE_ADMIN,
    ]);

    return count($items) > 0;
  }

  // ---------------------------------------------------------------------------
  // User
  // ---------------------------------------------------------------------------

  private function canCreateUser(TokenInterface $token) {
    return $this->decisionManager->decide($token, [Roles::ROLE_USER_ADMIN]);
  }

  private function canReadUser(User $user, TokenInterface $token) {
    if ($this->decisionManager->decide($token, [Roles::ROLE_USER_ADMIN])) {
      return TRUE;
    }

    // Any user can read itself.
    if ($user->getId() === $token->getUser()->getId()) {
      return TRUE;
    }

    return FALSE;
  }

  private function canUpdateUser(User $user, TokenInterface $token) {
    $roleNames = $this->securityManager->getReachableRoles($user);

    if (in_array(Roles::ROLE_SUPER_ADMIN, $roleNames) && !$this->securityManager->decide(Roles::ROLE_SUPER_ADMIN)) {
      return FALSE;
    }
    if (in_array(Roles::ROLE_ADMIN, $roleNames) && !$this->securityManager->decide(Roles::ROLE_ADMIN)) {
      return FALSE;
    }

    if ($this->decisionManager->decide($token, [Roles::ROLE_USER_ADMIN])) {
      return TRUE;
    }

    // Any user can update itself.
    if ($user->getId() === $token->getUser()->getId()) {
      return TRUE;
    }

    return FALSE;
  }

  private function canDeleteUser(User $user, TokenInterface $token) {
    return FALSE;
  }

  private function canListUser(TokenInterface $token) {
    return TRUE;
  }


  // ---------------------------------------------------------------------------
  // Groupable
  // ---------------------------------------------------------------------------

  private function canReadGroupable(GroupableEntity $groupable, TokenInterface $token) {
    $user = $token->getUser();

    if (method_exists($groupable, 'getUser') && $groupable->getUser() === $user->getId()) {
      return TRUE;
    }

    // @TODO: Check user's groups intersects with groupable's groups.
    $userGroupIds = $user->getUserGroups()->map(function (UserGroup $userGroup) {
      return $userGroup->getGroup()->getId();
    })->toArray();
    $groupableGroupIds = $groupable->getGroups()->map(function (Group $group) {
      return $group->getId();
    })->toArray();

    return !empty(array_intersect($userGroupIds, $groupableGroupIds));
  }

  private function canUpdateGroupable(GroupableEntity $groupable, TokenInterface $token) {
    return $this->canReadGroupable($groupable, $token);
  }

  private function canDeleteGroupable(GroupableEntity $groupable, TokenInterface $token) {
    return $this->canReadGroupable($groupable, $token);
  }
}
