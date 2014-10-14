<?php
/**
 * @file
 * ScreenGroup model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Extra
 *
 * @ORM\Table(name="screen_group")
 * @ORM\Entity
 */
class ScreenGroup {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api"})
   */
  private $id;

  /**
   * @ORM\Column(name="title", type="text", nullable=false)
   * @Groups({"api"})
   */
  private $title;

  /**
   * @ORM\ManyToMany(targetEntity="Screen", inversedBy="groups")
   * @ORM\JoinTable(name="screens_groups")
   * @Groups({"api"})
   */
  private $screens;

  /**
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   * @Groups({"api"})
   */
  private $created_at;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->screens = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set title
   *
   * @param string $title
   * @return ScreenGroup
   */
  public function setTitle($title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Add screens
   *
   * @param \Indholdskanalen\MainBundle\Entity\Screen $screens
   * @return ScreenGroup
   */
  public function addScreen(\Indholdskanalen\MainBundle\Entity\Screen $screens)
  {
    $this->screens[] = $screens;

    return $this;
  }

  /**
   * Remove screens
   *
   * @param \Indholdskanalen\MainBundle\Entity\Screen $screens
   */
  public function removeScreen(\Indholdskanalen\MainBundle\Entity\Screen $screens)
  {
    $this->screens->removeElement($screens);
  }

  /**
   * Get screens
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getScreens()
  {
    return $this->screens;
  }

  /**
   * Set created_at
   *
   * @param integer $createdAt
   * @return ScreenGroup
   */
  public function setCreatedAt($createdAt)
  {
    $this->created_at = $createdAt;

    return $this;
  }

  /**
   * Get created_at
   *
   * @return integer
   */
  public function getCreatedAt()
  {
    return $this->created_at;
  }
}
