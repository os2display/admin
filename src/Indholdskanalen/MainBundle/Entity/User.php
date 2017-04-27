<?php
/**
 * @file
 * Contains user class.
 */

namespace Indholdskanalen\MainBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Indholdskanalen\MainBundle\Entity\UserGroup;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_user")
 */
class User extends BaseUser {
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api"})
   */
  protected $id;

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
   * @Groups({"api"})
   */
  protected $userGroups;

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
}
