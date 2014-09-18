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
   * @ORM\Column(name="title", type="text", nullable=false)
   */
  private $title;

  /**
   * @ORM\Column(name="orientation", type="string", nullable=true)
   */
  private $orientation;

  /**
   * @ORM\Column(name="template", type="string", nullable=true)
   */
  private $template;

  /**
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   */
  private $created_at;

  /**
   * @ORM\Column(name="options", type="json_array", nullable=true)
   */
  private $options;

  /**
   * @ORM\Column(name="user", type="text", nullable=true)
   */
  private $user;

  /**
   * @ORM\Column(name="duration", type="integer", nullable=true)
   */
  private $duration;

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
   * Set template
   *
   * @param \string $template
   */
  public function setTemplate($template) {
    $this->template = $template;
  }

  /**
   * Get template
   *
   * @return \string
   */
  public function getTemplate() {
    return $this->template;
  }

  /**
   * Set user
   *
   * @param string $user
   */
  public function setUser($user) {
    $this->user = $user;
  }

  /**
   * Get user
   *
   * @return string
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * Set options
   *
   * @param string $options
   */
  public function setOptions($options) {
    $this->options = $options;
  }

  /**
   * Get options
   *
   * @return string
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * Set created_at
   *
   * @param integer $createdAt
   * @return Slide
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
   * Set duration
   *
   * @param integer $duration
   * @return Slide
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;

    return $this;
  }

  /**
   * Get duration
   *
   * @return integer
   */
  public function getDuration()
  {
    return $this->duration;
  }
}
