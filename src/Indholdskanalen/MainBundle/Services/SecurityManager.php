<?php

namespace Indholdskanalen\MainBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
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
    $token = $this->tokenStorage->getToken();
    if (!is_array($attributes)) {
      $attributes = [$attributes];
    }

    return $this->decisionManager->decide($token, $attributes, $object);
  }
}
