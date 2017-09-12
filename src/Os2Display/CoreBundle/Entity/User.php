<?php
/**
 * @file
 * Contains user class.
 */

namespace Os2Display\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use Os2Display\CoreBundle\Traits\ApiData;
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
   * @Groups({"api", "api-group"})
   */
  protected $groupRoles;

  /**
   * User's roles mapped to role display name.

   * @var array
   * @Groups({"api"})
   * @SerializedName("roles")
   */
  protected $roleNames;

  /**
   * @var Collection
   * @Groups({"api"})
   * @SerializedName("groups")
   */
  protected $roleGroups;

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
   * @VirtualProperty()
   * @SerializedName("displayName")
   * @Groups({"api"})
   */
  public function __toString() {
    if ($this->getFirstname() && $this->getLastname()) {
      return $this->getFirstname() . ' ' . $this->getLastname();
    }
    if ($this->getEmail()) {
      return $this->getEmail();
    }

    return 'user#' . $this->getId();
  }

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
   * Build groups with roles.
   *
   * @return array
   */
  public function buildRoleGroups($force = FALSE) {
    if ($this->roleGroups === NULL || $force) {
      $userGroups = $this->getUserGroups();
      $groups = [];
      foreach ($userGroups as $userGroup) {
        $group = $userGroup->getGroup();
        if (!isset($groups[$group->getId()])) {
          $groups[$group->getId()] = $group;
        }
        $groups[$group->getId()]->addRole($userGroup->getRole());
      }

      $this->roleGroups = array_values($groups);
    }

    return $this;
  }

  public function getRoleGroups() {
    $this->buildRoleGroups();

    return $this->roleGroups;
  }

  public function setRoleNames(array $roleNames) {
    $this->roleNames = array_unique($roleNames);
  }

  public function getRoleNames() {
    return $this->roleNames;
  }

  public function getRoles($includeGroupRoles = TRUE, $includeDefaultRole = TRUE) {
    $roles = $this->roles;

    if ($includeGroupRoles) {
      foreach ($this->getGroups() as $group) {
        $roles = array_merge($roles, $group->getRoles());
      }
    }

    if ($includeDefaultRole || count($roles) === 0) {
      // we need to make sure to have at least one role
      $roles[] = static::ROLE_DEFAULT;
    }

    return array_unique($roles);
  }

}
