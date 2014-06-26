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
   * @ORM\Column(name="created", type="integer", nullable=false)
   */
  private $created;

  /**
   * @ORM\Column(name="slides", type="array", nullable=true)
   */
  private $slides;

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
}
