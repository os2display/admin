<?php
/**
 * @file
 * Contains user group class.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="ik_group")
 */
class UserGroup {
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api"})
   */
  protected $id;

  /**
   * @ORM\Column(name="role", type="string", nullable=false)
   * @Groups({"api"})
   */
  private $role;

  /**
   * Channel to add.
   *
   * @ORM\ManyToOne(targetEntity="Group", inversedBy="userGroup")
   * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
   * @Groups({"api"})
   */
  protected $group;

  /**
   * Channel to add.
   *
   * @ORM\ManyToOne(targetEntity="User", inversedBy="userGroup")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
   * @Groups({"api"})
   */
  protected $user;

  /**
   * @return mixed
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return mixed
   */
  public function getRole() {
    return $this->role;
  }

  /**
   * @param mixed $role
   */
  public function setRole($role) {
    $this->role = $role;
  }

  /**
   * @return mixed
   */
  public function getGroup() {
    return $this->group;
  }

  /**
   * @param mixed $group
   */
  public function setGroup($group) {
    $this->group = $group;
  }
}
