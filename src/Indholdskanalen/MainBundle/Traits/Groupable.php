<?php

namespace Indholdskanalen\MainBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;

trait Groupable {
  /**
   * @var ArrayCollection
   * @Groups({"api", "api-bulk"})
   */
  protected $groups;

  /**
   * Returns the unique groupable resource type
   *
   * @return string
   */
  public function getGroupableType() {
    return get_class($this);
  }

  /**
   * Returns the unique groupable resource identifier
   *
   * @return string
   */
  public function getGroupableId() {
    return $this->getId();
  }

  /**
   * Returns the groups for this groupable entity
   *
   * @return ArrayCollection
   */
  public function getGroups() {
    $this->groups = $this->groups ?: new ArrayCollection();
    return $this->groups;
  }
}
