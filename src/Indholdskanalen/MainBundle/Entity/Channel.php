<?php
/**
 * @file
 * Channel model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Extra
 *
 * @ORM\Table(name="channel")
 * @ORM\Entity
 */
class Channel {
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
   * @ORM\Column(name="orientation", type="string", nullable=true)
   */
  private $orientation;

  /**
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   */
  private $created_at;

  /**
   * @ORM\Column(name="slides", type="array", nullable=true)
   */
  private $slides;

  /**
   * @ORM\ManyToMany(targetEntity="Screen", inversedBy="channels")
   * @ORM\JoinTable(name="screens_channels")
   */
  private $screens;

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
   * Set slides
   *
   * @param array $slides
   */
  public function setSlides($slides) {
    $this->slides = $slides;
  }

  /**
   * Get slides
   *
   * @return array
   */
  public function getSlides() {
    return $this->slides;
  }

  /**
   * Set created_at
   *
   * @param integer $createdAt
   * @return Channel
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

  /**
   * Add screen
   *
   * @param \Indholdskanalen\MainBundle\Entity\Screen $screen
   * @return ScreenGroup
   */
  public function addScreen(\Indholdskanalen\MainBundle\Entity\Screen $screen) {
    $this->screens[] = $screen;

    return $this;
  }

  /**
   * Remove screen
   *
   * @param \Indholdskanalen\MainBundle\Entity\Screen $screen
   */
  public function removeScreen(\Indholdskanalen\MainBundle\Entity\Screen $screen) {
    $this->screens->removeElement($screen);
  }

  /**
   * Get screens
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getScreens() {
    return $this->screens;
  }
}
