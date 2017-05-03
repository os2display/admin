<?php
/**
 * @file
 * Contains user class.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use Indholdskanalen\MainBundle\Traits\ApiData;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_user")
 */
class User extends BaseUser {
  use ApiData;

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api"})
   */
  protected $id;

  /**
   * @var array
   * @Groups({"api"})
   */
  protected $roles;

  /**
   * @var array
   * @Groups({"api", "api-group"})
   */
  protected $groupRoles;

  /**
   * @var Collection
   * @Groups({"api"})
   */
  protected $groups;

  /**
   * @var string
   * @Assert\NotBlank()
   * @Groups({"api"})
   */
  protected $username;

  /**
   * @var string
   * @Assert\NotBlank()
   * @Assert\Email()
   * @Groups({"api"})
   */
  protected $email;

  /**
   * @ORM\Column(name="firstname", type="string", nullable=true)
   * @Groups({"api"})
   */
  protected $firstname;

  /**
   * @ORM\Column(name="lastname", type="string", nullable=true)
   * @Groups({"api"})
   */
  protected $lastname;

  /**
   * @ORM\OneToMany(targetEntity="UserGroup", mappedBy="user", orphanRemoval=true)
   */
  protected $userGroups;

  /**
   * @Groups({"api"})
   * @SerializedName("groups")
   */
  protected $roleGroups;

  /**
   * Constructor
   */
  public function __construct() {
    parent::__construct();

    $this->userGroups = new ArrayCollection();
  }

  /**
   * Is the user administrator
   *
   * @return boolean
   *
   * @VirtualProperty
   * @SerializedName("is_admin")
   * @Groups({"api"})
   */
  public function isAdmin() {
    $result = FALSE;

    foreach ($this->getRoles() as $role) {
      if ($role == 'ROLE_ADMIN' || $role === 'ROLE_SUPER_ADMIN') {
        $result = TRUE;
      }
    }

    return $result;
  }

  /**
   * Is the user a super administrator
   *
   * @return boolean
   *
   * @VirtualProperty
   * @SerializedName("is_super_admin")
   * @Groups({"api"})
   */
  public function isSuperAdmin() {
    $result = FALSE;

    foreach ($this->getRoles() as $role) {
      if ($role == 'ROLE_SUPER_ADMIN') {
        $result = TRUE;
      }
    }

    return $result;
  }

  /**
   * Get id
   *
   * @return integer $id
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return mixed
   */
  public function getFirstname() {
    return $this->firstname;
  }

  /**
   * @param mixed $firstname
   */
  public function setFirstname($firstname) {
    $this->firstname = $firstname;
  }

  /**
   * @return mixed
   */
  public function getLastname() {
    return $this->lastname;
  }

  /**
   * @param mixed $lastname
   */
  public function setLastname($lastname) {
    $this->lastname = $lastname;
  }

  /**
   * Add userGroup
   *
   * @param UserGroup $userGroup
   * @return User
   */
  public function addUserGroup(UserGroup $userGroup) {
    $this->userGroups[] = $userGroup;

    return $this;
  }

  /**
   * Remove userGroup
   *
   * @param UserGroup $userGroup
   * @return User
   */
  public function removeUserGroup(UserGroup $userGroup) {
    $this->userGroups->removeElement($userGroup);

    return $this;
  }

  /**
   * Get userGroup
   *
   * @return Collection
   */
  public function getUserGroups() {
    return $this->userGroups;
  }

  /**
   * Build groupRoles, i.e. a list of roles within a group.
   */
  public function buildGroupRoles(Group $group, $force = FALSE) {
    if ($this->groupRoles === NULL || $force) {
      $groupRoles = [];
      $userGroups = $this->getUserGroups();
      foreach ($userGroups as $userGroup) {
        if ($userGroup->getGroup() == $group) {
          $groupRoles[] = $userGroup->getRole();
        }
      }

      $this->groupRoles = array_unique($groupRoles);
    }

    return $this;
  }

  /**
   * Build role groups.
   *
   * @return array
   */
  public function buildRoleGroups($force = FALSE) {
    if ($this->roleGroups === NULL || $force) {
      $userGroups = $this->getUserGroups();
      $roleGroups = [];
      foreach ($userGroups as $userGroup) {
        $group = $userGroup->getGroup();
        if (!isset($roleGroups[$group->getId()])) {
          $roleGroups[$group->getId()] = RoleGroup::create($group);
        }
        $roleGroup = $roleGroups[$group->getId()];
        $roleGroup->addRole($userGroup->getRole());
      }

      $this->roleGroups = array_values($roleGroups);
    }

    return $this;
  }

}
