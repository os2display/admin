<?php
/**
 * @file
 * Contains group class.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="ik_group")
 */
class Group extends ApiEntity {
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api", "search", "api-bulk", "channel", "slide", "media", "screen"})
   */
  protected $id;

  /**
   * @Assert\NotBlank()
   * @ORM\Column(name="title", type="string", nullable=false)
   * @Groups({"api", "api-bulk", "channel", "slide", "media", "screen"})
   */
  protected $title;

  /**
   * @ORM\OneToMany(targetEntity="UserGroup", mappedBy="group", orphanRemoval=true)
   */
  protected $userGroups;

  /**
   * @var array
   * @Groups({"api"})
   */
  protected $users;

  /**
   * @var array
   * @Groups({"api"})
   */
  protected $roles;

  /**
  * @ORM\OneToMany(targetEntity="Indholdskanalen\MainBundle\Entity\Grouping", mappedBy="group")
  */
  protected $grouping;

  /**
   * @VirtualProperty()
   * @SerializedName("displayName")
   * @Groups({"api", "channel", "slide", "media", "screen"})
   */
  public function __toString() {
    if ($this->getTitle()) {
      return $this->getTitle();
    }

    return 'group#' . $this->getId();
  }

  /**
   * Group constructor.
   */
  public function __construct() {
    $this->userGroups = new ArrayCollection();
    $this->roles = [];
  }

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
  public function addUserGroup(UserGroup $userGroup) {
    $this->userGroups[] = $userGroup;

    return $this;
  }

  /**
   * Remove userGroup
   *
   * @param \Indholdskanalen\MainBundle\Entity\UserGroup $userGroup
   * @return Group
   */
  public function removeUserGroup(UserGroup $userGroup) {
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

  public function buildUsers($force = FALSE) {
    if ($this->users === NULL || $force) {
      $users = [];
      $userGroups = $this->getUserGroups();
      foreach ($userGroups as $userGroup) {
        $user = $userGroup->getUser();
        if (!isset($users[$user->getId()])) {
          $users[$user->getId()] = $user;
        }
      }

      $users = array_values($users);
      foreach ($users as $user) {
        $user->buildGroupRoles($this);
      }
      $this->users = $users;
    }

    return $this;
  }

  public function getUsers() {
    return $this->users;
  }

  /**
   * @param string $role
   *
   * @return Group
   */
  public function addRole($role) {
    if ($this->roles === NULL) {
      $this->roles = [];
    }
    if (!$this->hasRole($role)) {
      $this->roles[] = strtoupper($role);
    }

    return $this;
  }

  /**
   * @param string $role
   */
  public function hasRole($role) {
    return in_array(strtoupper($role), $this->roles, TRUE);
  }

  public function getRoles() {
    return $this->roles;
  }

}
