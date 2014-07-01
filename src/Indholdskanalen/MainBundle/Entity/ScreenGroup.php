<?php
/**
 * @file
 * ScreenGroup model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
   */
  private $id;

  /**
   * @ORM\Column(name="title", type="text", nullable=false)
   */
  private $title;

  /**
   * @ORM\Column(name="screens", type="array", nullable=false)
   */
  private $screens;

  /**
   * @ORM\Column(name="created", type="integer", nullable=false)
   */
  private $created;


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
   * Set screens
   *
   * @param array $screens
   * @return ScreenGroup
   */
  public function setScreens($screens)
  {
    $this->screens = $screens;

    return $this;
  }

  /**
   * Get screens
   *
   * @return array
   */
  public function getScreens()
  {
    return $this->screens;
  }

  /**
   * Set created
   *
   * @param integer $created
   * @return ScreenGroup
   */
  public function setCreated($created)
  {
    $this->created = $created;

    return $this;
  }

  /**
   * Get created
   *
   * @return integer
   */
  public function getCreated()
  {
    return $this->created;
  }
}
