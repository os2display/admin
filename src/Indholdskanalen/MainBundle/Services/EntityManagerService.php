<?php

namespace Indholdskanalen\MainBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\User;
use Indholdskanalen\MainBundle\Entity\UserGroup;
use Indholdskanalen\MainBundle\Security\Roles;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class EntityManagerService
 *
 * Service to help finding entities that the current user can view/edit.
 *
 * @package Indholdskanalen\MainBundle\Services
 */
class EntityManagerService {
  protected $manager;
  protected $tokenStorage;
  protected $authorizationChecker ;

  public function __construct(EntityManagerInterface $manager, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker) {
    $this->manager = $manager;
    $this->tokenStorage = $tokenStorage;
    $this->authorizationChecker = $authorizationChecker;
  }

  public function findAll($class) {
    return $this->findBy($class, []);
  }

  public function findBy($class, array $criteria, array $orderBy = null, $limit = null, $offset = null) {
    $repository = $this->manager->getRepository($class);
    $this->addCriteria($class, $criteria);
    return $repository->findBy($criteria, $orderBy, $limit, $offset);
  }

  private function addCriteria($class, array &$criteria = NULL) {
    if ($this->authorizationChecker->isGranted(Roles::ROLE_ADMIN)) {
      return;
    }

    $user = $this->tokenStorage->getToken()->getUser();
    if (!$user) {
      $criteria['id'] = [];
    }

    switch ($class) {
      case Group::class:
        return $this->addCriteriaGroup($criteria, $user);
      case User::class:
        return $this->addCriteriaUser($criteria, $user);
    }
  }

  private function addCriteriaGroup(array &$criteria, User $user) {
    if ($this->authorizationChecker->isGranted(Roles::ROLE_GROUP_ADMIN)) {
      return;
    }

    // Find all groups in which current user is member.
    $builder = $this->manager->createQueryBuilder();
    $query = $builder
      ->select('g')
      ->from(UserGroup::class, 'g')
      ->where('g.user = :user')
      ->getQuery();
    $result = $query->setParameters([
      'user' => $user,
    ])->getResult();

    $ids = [];
    foreach ($result as $userGroup) {
      $ids[] = $userGroup->getGroup()->getId();
    }
    if (isset($criteria['id'])) {
      $ids = array_intersect($ids, $criteria['id']);
    }
    $criteria['id'] = $ids;
  }


  private function addCriteriaUser(array &$criteria, User $user) {
    // @TODO
  }
}
