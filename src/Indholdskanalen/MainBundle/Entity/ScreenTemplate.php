<?php
/**
 * @file
 * Screen template.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Screen template entity.
 *
 * @ORM\Table(name="ik_screen_templates")
 * @ORM\Entity
 */
class ScreenTemplate {
  /**
   * @ORM\Column(type="string")
   * @ORM\Id
   */
  protected $id;

  /**
   * @ORM\Column(name="name", type="string")
   * @Groups({"api"})
   */
  protected $name;

  /**
   * @ORM\Column(name="path_icon", type="string")
   */
  protected $pathIcon;

  /**
   * @ORM\Column(name="path_live", type="string")
   */
  protected $pathLive;

  /**
   * @ORM\Column(name="path_edit", type="string")
   */
  protected $pathEdit;

  /**
   * @ORM\Column(name="path_preview", type="string")
   */
  protected $pathPreview;

  /**
   * @ORM\Column(name="path_css", type="string")
   */
  protected $pathCss;

  /**
   * @ORM\Column(name="orientation", type="string")
   * @Groups({"api"})
   */
  protected $orientation;

  /**
   * @ORM\Column(name="width", type="integer")
   * @Groups({"api"})
   */
  protected $width;

  /**
   * @ORM\Column(name="height", type="integer")
   * @Groups({"api"})
   */
  protected $height;

  /**
   * @ORM\OneToMany(targetEntity="Screen", mappedBy="template")
   * @Groups({"api"})
   */
  protected $screens;

  /**
   * Get the paths virtual property.
   *
   * @return array
   *
   * @VirtualProperty
   * @SerializedName("paths")
   * @Groups({"api"})
   */
  public function getChannels() {
    $result = array(
      'icon' => $this->getPathIcon(),
      'live' => $this->getPathLive(),
      'edit' => $this->getPathEdit(),
      'preview' => $this->getPathPreview(),
      'css' => $this->getPathCss()
    );
    return $result;
  }

  /**
   * Set id
   *
   * @param string $id
   *
   * @return ScreenTemplate
   */
  public function setId($id) {
    $this->id = $id;

    return $this;
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
   * Set name
   *
   * @param string $name
   * @return ScreenTemplate
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set pathIcon
   *
   * @param string $pathIcon
   * @return ScreenTemplate
   */
  public function setPathIcon($pathIcon) {
    $this->pathIcon = $pathIcon;

    return $this;
  }

  /**
   * Get pathIcon
   *
   * @return string
   */
  public function getPathIcon() {
    return $this->pathIcon;
  }

  /**
   * Set pathLive
   *
   * @param string $pathLive
   * @return ScreenTemplate
   */
  public function setPathLive($pathLive) {
    $this->pathLive = $pathLive;

    return $this;
  }

  /**
   * Get pathLive
   *
   * @return string
   */
  public function getPathLive() {
    return $this->pathLive;
  }

  /**
   * Set pathEdit
   *
   * @param string $pathEdit
   * @return ScreenTemplate
   */
  public function setPathEdit($pathEdit) {
    $this->pathEdit = $pathEdit;

    return $this;
  }

  /**
   * Get pathEdit
   *
   * @return string
   */
  public function getPathEdit() {
    return $this->pathEdit;
  }

  /**
   * Set pathPreview
   *
   * @param string $pathPreview
   * @return ScreenTemplate
   */
  public function setPathPreview($pathPreview) {
    $this->pathPreview = $pathPreview;

    return $this;
  }

  /**
   * Get pathPreview
   *
   * @return string
   */
  public function getPathPreview() {
    return $this->pathPreview;
  }

  /**
   * Set pathCss
   *
   * @param string $pathCss
   * @return ScreenTemplate
   */
  public function setPathCss($pathCss) {
    $this->pathCss = $pathCss;

    return $this;
  }

  /**
   * Get pathCss
   *
   * @return string
   */
  public function getPathCss() {
    return $this->pathCss;
  }

  /**
   * Set orientation
   *
   * @param string $orientation
   * @return ScreenTemplate
   */
  public function setOrientation($orientation) {
    $this->orientation = $orientation;

    return $this;
  }

  /**
   * Get orientation
   *
   * @return string
   */
  public function getOrientation() {
    return $this->orientation;
  }

  /**
   * Set width
   *
   * @param integer $width
   * @return ScreenTemplate
   */
  public function setWidth($width) {
    $this->width = $width;

    return $this;
  }

  /**
   * Get width
   *
   * @return integer
   */
  public function getWidth() {
    return $this->width;
  }

  /**
   * Set height
   *
   * @param integer $height
   * @return ScreenTemplate
   */
  public function setHeight($height) {
    $this->height = $height;

    return $this;
  }

  /**
   * Get height
   *
   * @return integer
   */
  public function getHeight() {
    return $this->height;
  }
}
