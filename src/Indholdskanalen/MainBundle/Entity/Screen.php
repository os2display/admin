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
   * @ORM\Column(name="groups", type="array", nullable=false)
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
  public function getId() {
    return $this->id;
  }

  /**
   * Set title
   *
   * @param string $title
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Set orientation
   *
   * @param \int $orientation
   */
  public function setOrientation($orientation) {
    $this->orientation = $orientation;
  }

  /**
   * Get orientation
   *
   * @return \string
   */
  public function getOrientation() {
    return $this->orientation;
  }

  /**
   * Set width
   *
   * @param \int $width
   */
  public function setWidth($width) {
    $this->width = $width;
  }

  /**
   * Get width
   *
   * @return \string
   */
  public function getWidth() {
    return $this->width;
  }

  /**
   * Set height
   *
   * @param \int $height
   */
  public function setHeight($height) {
    $this->height = $height;
  }

  /**
   * Get height
   *
   * @return \string
   */
  public function getHeight() {
    return $this->height;
  }

  /**
   * Set created
   *
   * @param \int $created
   */
  public function setCreated($created) {
    $this->created = $created;
  }

  /**
   * Get created
   *
   * @return \int
   */
  public function getCreated() {
    return $this->created;
  }

  /**
   * Set groups
   *
   * @param array $groups
   * @return Screen
   */
  public function setGroups($groups)
  {
    $this->groups = $groups;

    return $this;
  }

  /**
   * Get groups
   *
   * @return array
   */
  public function getGroups()
  {
    return $this->groups;
  }
}
