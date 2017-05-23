<?php

namespace Indholdskanalen\MainBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Indholdskanalen\MainBundle\Entity\GroupableEntity;
use Indholdskanalen\MainBundle\Security\Roles;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GroupingFilter extends SQLFilter implements ContainerAwareInterface {
  public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias) {
    if (!$targetEntity->reflClass->isSubclassOf(GroupableEntity::class)) {
      return '';
    }

    if ($this->container->get('security.authorization_checker')->isGranted(Roles::ROLE_ADMIN)) {
      return '';
    }

    $tableName = $targetEntity->getTableName();
    $class = $targetEntity->getName();
    $user = $this->container->get('security.token_storage')->getToken()->getUser();

    $queries = [];
    if ($targetEntity->hasField('user')) {
      // Users can see all groupables created by themselves
      $queries[] = 'select id from ' . $tableName . ' where user = :user_id';
    }
    // Plus all groupables in groups they are a member of
    $queries[] = 'select entity_id id from ik_grouping where entity_type = :entity_type and group_id in (select group_id from ik_user_group where user_id = :user_id)';

    $sql = join(' union ', $queries);
    $result = $this->getConnection()->fetchAll($sql, [
      'entity_type' => $class,
      'user_id' => $user->getId(),
    ]);
    $ids = array_map(function ($row) {
      return $row['id'];
    }, $result);
    if (empty($ids)) {
      $ids[] = 0;
    }

    return $targetTableAlias.'.id in (' . implode(',', $ids) . ')';
  }

  /**
   * @var ContainerInterface
   */
  protected $container;

  public function setContainer(ContainerInterface $container = NULL) {
    // @see http://stackoverflow.com/a/14650403
    $this->container = $container;
  }
}
