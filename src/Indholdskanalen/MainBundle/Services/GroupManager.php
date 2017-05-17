<?php
/**
 * @file
 * Contains the group manager.
 */

namespace Indholdskanalen\MainBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Indholdskanalen\MainBundle\Entity\Group;
use Indholdskanalen\MainBundle\Entity\GroupableEntity;
use Indholdskanalen\MainBundle\Entity\Grouping;
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

  public function replaceGroups($groups, GroupableEntity $groupable) {
    $groups = $this->loadGroups($groups);
    $groupable->getGroups()->clear();
    $this->addGroups($groups, $groupable);
  }

  /**
   * Convert list of "groups", i.e.
   *
   *   - list of Groups,
   *   - list of ids,
   *   - list of objects/arrays with "id" key,
   *
   * into a list of proper Groups.
   */
  private function loadGroups($groups) {
    $ids = [];
    foreach ($groups as $group) {
      $id = null;
      if ($group instanceof Group) {
        $id = $group->getId();
      } elseif (is_numeric($group)) {
        $id = $group;
      } elseif (isset($group->id)) {
        $id = $group->id;
      } elseif (isset($group['id'])) {
        $id = $group['id'];
      }
      if ($id !== null) {
        $ids[] = $id;
      }
    }

    return $this->entityManager->getRepository(Group::class)->findBy(['id' => $ids]);
  }

  public function addGroup(Group $group, GroupableEntity $groupable) {
    $groupable->getGroups()->add($group);
  }

  public function addGroups(array $groups, GroupableEntity $groupable) {
    foreach ($groups as $group) {
      if ($group instanceof Group) {
        $this->addGroup($group, $groupable);
      }
    }
  }

  public function saveGrouping(GroupableEntity $groupable) {
    $oldGroups = $this->getGrouping($groupable);
    $newGroups = $groupable->getGroups();
    $groupsToAdd = $newGroups;

    if ($oldGroups !== NULL and is_array($oldGroups) and !empty($oldGroups)) {
      $groupsToRemove = [];

      foreach ($oldGroups as $oldGroup) {
        if ($newGroups->exists(function ($index, $newGroup) use ($oldGroup) {
          return $newGroup->getId() == $oldGroup->getId();
        })) {
          $groupsToAdd->removeElement($oldGroup);
        }
        else {
          $groupsToRemove[] = $oldGroup->getId();
        }
      }

      if (count($groupsToRemove) > 0) {
        $builder = $this->entityManager->createQueryBuilder();
        $builder
          ->delete(Grouping::class, 't')
          ->where('t.group_id')
          ->where($builder->expr()->in('t.group', $groupsToRemove))
          ->andWhere('t.entityType = :type')
          ->setParameter('type', $groupable->getGroupableType())
          ->andWhere('t.entityId = :id')
          ->setParameter('id', $groupable->getGroupableId())
          ->getQuery()
          ->getResult();
      }
    }

    foreach ($groupsToAdd as $group) {
      $this->entityManager->persist(new Grouping($group, $groupable));
    }

    if (count($groupsToAdd)) {
      $this->entityManager->flush();
    }
  }

  /**
   * Loads all groups for the given groupable resource
   *
   * @param GroupableEntity  $groupable   GroupableEntity resource
   */
  public function loadGrouping(GroupableEntity $groupable) {
    $groups = $this->getGrouping($groupable);
    $this->replaceGroups($groups, $groupable);
  }

  /**
   * Gets all groups for the given groupable resource
   *
   * @param GroupableEntity  $groupable   GroupableEntity resource
   */
  protected function getGrouping(GroupableEntity $groupable) {
    return $this->entityManager
      ->createQueryBuilder()
      ->select('g')
      ->from(Group::class, 'g')
      ->join('g.grouping', 't', Join::WITH, 't.entityType = :type AND t.entityId = :id')
      ->setParameters([
        'type' => $groupable->getGroupableType(),
        'id' => $groupable->getGroupableId(),
      ])
      ->getQuery()
      ->getResult();
  }

  /**
   * Deletes all grouping records for the given groupable resource
   *
   * @param GroupableEntity  $groupable   GroupableEntity resource
   */
  public function deleteGrouping(GroupableEntity $groupable) {
    $groupingList = $this->entityManager->createQueryBuilder()
      ->select('t')
      ->from(Grouping::class, 't')
      ->where('t.entityType = :type')
      ->andWhere('t.entityId = :id')
      ->setParameters([
        'type' => $groupable->getGroupableType(),
        'id' => $groupable->getGroupableId(),
      ])
      ->getQuery()
      ->getResult();

    foreach ($groupingList as $grouping) {
      $this->entityManager->remove($grouping);
    }
  }

}
