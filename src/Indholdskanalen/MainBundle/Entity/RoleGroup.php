<?php

namespace Indholdskanalen\MainBundle\Entity;

use FOS\UserBundle\Entity\Group as BaseGroup;
use JMS\Serializer\Annotation as Serializer;

class RoleGroup extends BaseGroup {
  /**
   * @Serializer\Groups({"api"})
   */
  protected $id;

  /**
   * @Serializer\Groups({"api"})
   */
  protected $name;

  /**
   * @Serializer\Groups({"api"})
   */
  protected $roles;

  protected function setId($id) {
    $this->id = $id;
  }

  public static function create(Group $group) {
    $roleGroup = new RoleGroup($group->getTitle());
    $roleGroup->setId($group->getId());

    return $roleGroup;
  }
}
