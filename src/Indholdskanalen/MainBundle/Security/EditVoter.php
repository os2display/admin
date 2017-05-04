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

  const CREATE = 'create';
  const READ = 'read';
  const UPDATE = 'update';
  const DELETE = 'delete';

  public function __construct(EntityManagerInterface $manager, AccessDecisionManagerInterface $decisionManager) {
    $this->manager = $manager;
    $this->decisionManager = $decisionManager;
  }

  protected function supports($attribute, $subject) {
    // if the attribute isn't one we support, return false
    if (!in_array($attribute, [self::CREATE, self::READ, self::UPDATE, self::DELETE])) {
      return FALSE;
    }

    return $subject instanceof Group || $subject instanceof User;
  }

  protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
    if ($this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
      return TRUE;
    }

    $user = $token->getUser();
    if (!$user instanceof User) {
      // the user must be logged in; if not, deny access
      return FALSE;
    }

    switch ($attribute) {
      //      case self::CREATE:
      //        return $this->canCreate($subject, $user);
      case self::READ:
        return $this->canRead($subject, $user);

      case self::UPDATE:
        return $this->canUpdate($subject, $user);

      case self::DELETE:
        return $this->canDelete($subject, $user);
    }

    throw new \LogicException('This code should not be reached!');
  }

  private function canRead($subject, User $user) {
    if ($subject instanceof Group) {
      return $this->canReadGroup($subject, $user);
    }
    elseif ($subject instanceof User) {
      return $this->canReadUser($subject, $user);
    }

    throw new \LogicException('This code should not be reached!');
  }

  private function canUpdate($subject, User $user) {
    if ($subject instanceof Group) {
      return $this->canUpdateGroup($subject, $user);
    }
    elseif ($subject instanceof User) {
      return $this->canUpdateUser($subject, $user);
    }

    throw new \LogicException('This code should not be reached!');
  }

  private function canDelete($subject, User $user) {
    if ($subject instanceof Group) {
      return $this->canDeleteGroup($subject, $user);
    }
    elseif ($subject instanceof User) {
      return $this->canDeleteUser($subject, $user);
    }

    throw new \LogicException('This code should not be reached!');
  }

  // ---------------------------------------------------------------------------
  // Group
  // ---------------------------------------------------------------------------

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
      'role' => GroupRoles::ROLE_GROUP_GROUP_ADMIN,
    ]);

    return count($roles) > 0;
  }

  private function canDeleteGroup(Group $group, User $user) {
    return $this->canUpdateGroup($group, $user);
  }

  // ---------------------------------------------------------------------------
  // User
  // ---------------------------------------------------------------------------

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

}
