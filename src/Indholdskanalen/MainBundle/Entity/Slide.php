<?php
/**
 * @file
 * Slide model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Extra
 *
 * @ORM\Table(name="slide")
 * @ORM\Entity
 */
class Slide {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var integer
   *
   * @ORM\Column(name="title", type="text", nullable=false)
   */
  private $title;

  /**
   * @var integer
   *
   * @ORM\Column(name="orientation", type="integer", nullable=false)
   */
  private $orientation;

  /**
   * @var integer
   *
   * @ORM\Column(name="created", type="integer", nullable=false)
   */
  private $created;

  /**
   * @var string
   *
   * @ORM\Column(name="options", type="text", nullable=true)
   */
  private $options;

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
   *
   * @return Slide
   */
  public function setTitle($title) {
    $this->title = $title;

    return $this;
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
   *
   * @return Slide
   */
  public function setOrientation($orientation) {
    $this->orientation = $orientation;

    return $this;
  }

  /**
   * Get orientation
   *
   * @return \int
   */
  public function getOrientation() {
    return $this->orientation;
  }

  /**
   * Set created
   *
   * @param \int $created
   *
   * @return Slide
   */
  public function setCreated($created) {
    $this->created = $created;

    return $this;
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
   * Set options
   *
   * @param string $options
   *
   * @return Slide
   */
  public function setOptions($options) {
    $this->options = $options;

    return $this;
  }

  /**
   * Get options
   *
   * @return string
   */
  public function getOptions() {
    return $this->options;
  }
}
