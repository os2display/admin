<?php

namespace Indholdskanalen\MainBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Security\EditVoter;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;

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
   * @var \Symfony\Component\Security\Core\Authorization\AccessDecisionManager
   */
  protected $decisionManager;

  public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $manager, AccessDecisionManager $decisionManager) {
    $this->tokenStorage = $tokenStorage;
    $this->manager = $manager;
    $this->decisionManager = $decisionManager;
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
}
