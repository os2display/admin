<?php
/**
 * @file
 * Contains the group manager.
 */

namespace Os2Display\CoreBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Os2Display\CoreBundle\Entity\Group;
use Os2Display\CoreBundle\Entity\GroupableEntity;
use Os2Display\CoreBundle\Entity\Grouping;
use Os2Display\CoreBundle\Entity\UserGroup;
use Os2Display\CoreBundle\Exception\DuplicateEntityException;
use Os2Display\CoreBundle\Security\Roles;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GroupManager
 * @package Os2Display\CoreBundle\Services
 */
class GroupManager {
  protected static $editableProperties = ['title'];

  protected $entityManager;

  protected $entityService;

  protected $container;

  /**
   * GroupManager constructor.
   *
   * @param \Doctrine\ORM\EntityManagerInterface $entityManager
   * @param \Os2Display\CoreBundle\Services\EntityService $entityService
   */
  public function __construct(EntityManagerInterface $entityManager, EntityService $entityService, ContainerInterface $container) {
    $this->entityManager = $entityManager;
    $this->entityService = $entityService;
    $this->container = $container;
  }

  /**
   * Create a group.
   *
   * @param $data
   * @return Group
   * @throws \Os2Display\CoreBundle\Exception\DuplicateEntityException
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
   * @param \Os2Display\CoreBundle\Services\Group $group
   * @param $data
   * @return Group
   * @throws \Os2Display\CoreBundle\Exception\DuplicateEntityException
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

  /**
   * Set groups on a groupable entity.
   *
   * A user can only assign groups that he's a member of.
   * Any exiting groups on the entity that the user is no member of will be kept on the entity.
   *
   * @param array $groups
   *   The groups (@see GroupManager::loadGroups()).
   * @param \Os2Display\CoreBundle\Entity\GroupableEntity $entity
   *   The entity.
   */
  public function setGroups(array $groups, GroupableEntity $entity) {
    $securityManager = $this->container->get('os2display.security_manager');
    $user = $securityManager->getUser();

    // Get the groups to add to entity.
    $groups = new ArrayCollection($this->loadGroups($groups));

    if (!$securityManager->hasRole($user, Roles::ROLE_ADMIN)) {
      $userGroupIds = $user->getUserGroups()->map(function (UserGroup $userGroup) {
        return $userGroup->getGroup()->getId();
      })->toArray();

      // Remove any groups that the user is not member of.
      $groups = $groups->filter(function (Group $group) use ($userGroupIds) {
        return in_array($group->getId(), $userGroupIds);
      });

      // Get existing entity groups that the current user is not a member of.
      $additionalGroups = $entity->getGroups()->filter(function (Group $group) use ($userGroupIds) {
        return !in_array($group->getId(), $userGroupIds);
      });

      // Add the additional groups.
      foreach ($additionalGroups as $group) {
        $groups->add($group);
      }
    }

    $entity->setGroups($groups);
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
    // We'll potentially remove groups form the collection, so we make a copy in order to not destroy the group collection on the entity.
    $groupsToAdd = clone $newGroups;

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
