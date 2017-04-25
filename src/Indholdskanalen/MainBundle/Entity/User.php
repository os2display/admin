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
   * @Groups({"api"})
   */
  protected $roles;

  /**
   * Get id
   *
   * @return integer $id
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Constructor
   */
  public function __construct() {
    parent::__construct();
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
}
