<?php
/**
 * @file
 * Screen model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Extra
 *
 * @ORM\Table(name="screen")
 * @ORM\Entity
 */
class Screen {
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
   * @ORM\Column(name="orientation", type="string", nullable=false)
   */
  private $orientation;

  /**
   * @ORM\Column(name="width", type="integer", nullable=false)
   */
  private $width;

  /**
   * @ORM\Column(name="height", type="integer", nullable=false)
   */
  private $height;

  /**
   * @ORM\ManyToMany(targetEntity="ScreenGroup", mappedBy="screens")
   */
  private $groups;

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
   * @return Screen
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
   * Set orientation
   *
   * @param string $orientation
   * @return Screen
   */
  public function setOrientation($orientation)
  {
    $this->orientation = $orientation;

    return $this;
  }

  /**
   * Get orientation
   *
   * @return string
   */
  public function getOrientation()
  {
    return $this->orientation;
  }

  /**
   * Set width
   *
   * @param integer $width
   * @return Screen
   */
  public function setWidth($width)
  {
    $this->width = $width;

    return $this;
  }

  /**
   * Get width
   *
   * @return integer
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * Set height
   *
   * @param integer $height
   * @return Screen
   */
  public function setHeight($height)
  {
    $this->height = $height;

    return $this;
  }

  /**
   * Get height
   *
   * @return integer
   */
  public function getHeight()
  {
    return $this->height;
  }

  /**
   * Set created
   *
   * @param integer $created
   * @return Screen
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
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Add groups
   *
   * @param \Indholdskanalen\MainBundle\Entity\ScreenGroup $groups
   * @return Screen
   */
  public function addGroup(\Indholdskanalen\MainBundle\Entity\ScreenGroup $groups)
  {
    $groups->addScreen($this);
    $this->groups[] = $groups;

    return $this;
  }

  /**
   * Remove groups
   *
   * @param \Indholdskanalen\MainBundle\Entity\ScreenGroup $groups
   */
  public function removeGroup(\Indholdskanalen\MainBundle\Entity\ScreenGroup $groups)
  {
    $groups->removeScreen($this);
    $this->groups->removeElement($groups);
  }

  /**
   * Get groups
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getGroups()
  {
    return $this->groups;
  }
}
