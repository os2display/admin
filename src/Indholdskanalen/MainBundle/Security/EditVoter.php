<?php

namespace Indholdskanalen\MainBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\User;
use Indholdskanalen\MainBundle\Entity\UserGroup;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EditVoter extends Voter {
  protected $manager;
  protected $decisionManager;

  const CREATE = 'CREATE';
  const READ = 'READ';
  const UPDATE = 'UPDATE';
  const DELETE = 'DELETE';
  const READ_LIST = 'LIST';

  public function __construct(EntityManagerInterface $manager, AccessDecisionManagerInterface $decisionManager) {
    $this->manager = $manager;
    $this->decisionManager = $decisionManager;
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
        return $this->canRead($subject, $user);

      case self::UPDATE:
        return $this->canUpdate($subject, $user);

      case self::DELETE:
        return $this->canDelete($subject, $user);

      case self::READ_LIST:
        return $this->canList($subject, $user);
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

  private function canRead($subject, User $user) {
    if ($subject instanceof Group) {
      return $this->canReadGroup($subject, $user);
    }
    elseif ($subject instanceof User) {
      return $this->canReadUser($subject, $user);
    }

    return FALSE;
  }

  private function canUpdate($subject, User $user) {
    if ($subject instanceof Group) {
      return $this->canUpdateGroup($subject, $user);
    }
    elseif ($subject instanceof User) {
      return $this->canUpdateUser($subject, $user);
    }

    return FALSE;
  }

  private function canDelete($subject, User $user) {
    if ($subject instanceof Group) {
      return $this->canDeleteGroup($subject, $user);
    }
    elseif ($subject instanceof User) {
      return $this->canDeleteUser($subject, $user);
    }

    return FALSE;
  }

  private function canList($type, User $user) {
    switch ($type) {
      case Group::class:
      case 'group':
        return $this->canListGroup($user);
      case User::class:
      case 'user':
        return $this->canListUser($user);
    }

    return FALSE;
  }

  // ---------------------------------------------------------------------------
  // Group
  // ---------------------------------------------------------------------------

  private function canCreateGroup(TokenInterface $token) {
    return $this->decisionManager->decide($token, [Roles::ROLE_GROUP_ADMIN]);
  }

  private function canReadGroup(Group $group, User $user) {
    $roles = $this->manager->getRepository(UserGroup::class)->findBy([
      'group' => $group,
      'user' => $user,
    ]);

    return count($roles) > 0;
  }

  private function canUpdateGroup(Group $group, User $user) {
    $roles = $this->manager->getRepository(UserGroup::class)->findBy([
      'group' => $group,
      'user' => $user,
      'role' => GroupRoles::ROLE_GROUP_ROLE_ADMIN,
    ]);

    return count($roles) > 0;
  }

  private function canDeleteGroup(Group $group, User $user) {
    return $this->canUpdateGroup($group, $user);
  }

  private function canListGroup(User $user) {
    // A user can list groups if he is member of a group.
    $items = $this->manager->getRepository(UserGroup::class)->findBy([
      'user' => $user,
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

  private function canReadUser(User $user, User $currentUser) {
    if ($user->getId() === $currentUser->getId()) {
      return TRUE;
    }

    return FALSE;
  }

  private function canUpdateUser(User $user, User $currentUser) {
    if ($user->getId() === $currentUser->getId()) {
      return TRUE;
    }

    return FALSE;
  }

  private function canDeleteUser(User $user, User $currentUser) {
    return FALSE;
  }

  private function canListUser(User $user) {
    return TRUE;
  }

}
