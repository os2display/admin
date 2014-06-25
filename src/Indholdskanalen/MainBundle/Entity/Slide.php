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
   * @ORM\Column(name="created", type="integer", nullable=false)
   */
  private $created;

  /**
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
}
