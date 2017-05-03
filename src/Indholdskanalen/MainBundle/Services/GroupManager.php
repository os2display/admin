<?php
/**
 * @file
 * Contains the group manager.
 */

namespace Indholdskanalen\MainBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use FOS\GroupBundle\Doctrine\GroupManager as FOSGroupManager;
use FOS\GroupBundle\Util\TokenGeneratorInterface;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Exception\DuplicateEntityException;

/**
 * Class GroupManager
 * @package Indholdskanalen\MainBundle\Services
 */
class GroupManager {
  protected static $editableProperties = ['title'];

  protected $entityManager;

  protected $entityService;

  /**
   * GroupManager constructor.
   *
   * @param \Doctrine\ORM\EntityManagerInterface $entityManager
   * @param \Indholdskanalen\MainBundle\Services\EntityService $entityService
   */
  public function __construct(EntityManagerInterface $entityManager, EntityService $entityService) {
    $this->entityManager = $entityManager;
    $this->entityService = $entityService;
  }

  /**
   * Create a group.
   *
   * @param $data
   * @return Group
   * @throws \Indholdskanalen\MainBundle\Exception\DuplicateEntityException
   */
  public function createGroup($data) {
    $group = new Group();
    $this->entityService->setValues($group, $data, self::$editableProperties);
    $this->entityService->validateEntity($group);

    $repository = $this->entityManager->getRepository(Group::class);
    if ($repository->findBy(['title' => $group->getTitle()])) {
      throw new DuplicateEntityException('Group already exists.', $data);
    }

    $this->entityManager->persist($group);
    $this->entityManager->flush();

    return $group;
  }

  /**
   * Update a group.
   *
   * @param \Indholdskanalen\MainBundle\Services\Group $group
   * @param $data
   * @return Group
   * @throws \Indholdskanalen\MainBundle\Exception\DuplicateEntityException
   */
  public function updateGroup(Group $group, $data) {
    $this->entityService->setValues($group, $data, self::$editableProperties);
    $this->entityService->validateEntity($group);

    $repository = $this->entityManager->getRepository(Group::class);
    $anotherGroup = $repository->findOneBy(['title' => $group->getTitle()]);
    if ($anotherGroup && $anotherGroup->getId() !== $group->getId()) {
      throw new DuplicateEntityException('Group already exists.', $data);
    }

    $this->entityManager->persist($group);
    $this->entityManager->flush();

    return $group;
  }

  public function findGroupBy(array $criteria) {
    return $this->entityManager->getRepository(Group::class)->findOneBy($criteria);
  }

}
