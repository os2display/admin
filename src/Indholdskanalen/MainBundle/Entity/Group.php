<?php
/**
 * @file
 * Contains group class.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="ik_group")
 */
class Group {
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api"})
   */
  protected $id;

  /**
   * @Assert\NotNull()
   * @ORM\Column(name="title", type="string", nullable=false)
   * @Groups({"api"})
   */
  private $title;

  /**
   * @ORM\OneToMany(targetEntity="UserGroup", mappedBy="group", orphanRemoval=true)
   * @Groups({"api"})
   */
  private $userGroups;

  /**
   * @return mixed
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return mixed
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param mixed $title
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * Add userGroup
   *
   * @param \Indholdskanalen\MainBundle\Entity\UserGroup $userGroup
   * @return Group
   */
  public function addUserGroup(\Indholdskanalen\MainBundle\Entity\UserGroup $userGroup) {
    $this->userGroups[] = $userGroup;

    return $this;
  }

  /**
   * Remove userGroup
   *
   * @param \Indholdskanalen\MainBundle\Entity\UserGroup $userGroup
   * @return Group
   */
  public function removeUserGroup(\Indholdskanalen\MainBundle\Entity\UserGroup $userGroup) {
    $this->userGroups->removeElement($userGroup);

    return $this;
  }

  /**
   * Get userGroup
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getUserGroups() {
    return $this->userGroups;
  }
}
