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
   * @Groups({"api", "middleware"})
   */
  protected $id;

  /**
   * @ORM\Column(name="name", type="string")
   * @Groups({"api"})
   */
  protected $name;

  /**
   * @ORM\Column(name="path_icon", type="string")
   * @Groups({"api", "api-bulk"})
   */
  protected $pathIcon;

  /**
   * @ORM\Column(name="path_live", type="string")
   * @Groups({"middleware"})
   */
  protected $pathLive;

  /**
   * @ORM\Column(name="path_edit", type="string")
   */
  protected $pathEdit;

  /**
   * @ORM\Column(name="path_css", type="string")
   * @Groups({"middleware"})
   */
  protected $pathCss;

  /**
   * @ORM\Column(name="path", type="string")
   * @Groups({"middleware"})
   */
  protected $path;

  /**
   * @ORM\Column(name="tools", type="string")
   */
  protected $tools;

  /**
   * @ORM\Column(name="orientation", type="string")
   * @Groups({"api", "api-bulk"})
   */
  protected $orientation;

  /**
   * @ORM\OneToMany(targetEntity="Screen", mappedBy="template")
   */
  protected $screens;

  /**
   * @ORM\Column(name="enabled", type="boolean")
   * @Groups({"api", "api-bulk"})
   */
  protected $enabled;


  /**
   * Constructor
   */
  public function __construct() {
    $this->screens = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Get the paths virtual property.
   *
   * @return array
   *
   * @VirtualProperty
   * @SerializedName("paths")
   * @Groups({"api"})
   */
  public function getPaths() {
    $result = array(
      'icon' => $this->getPathIcon(),
      'live' => $this->getPathLive(),
      'edit' => $this->getPathEdit(),
      'css' => $this->getPathCss(),
      'path' => $this->getPath(),
    );
    return $result;
  }

  /**
   * @return mixed
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * @param mixed $path
   * @return ScreenTemplate
   */
  public function setPath($path) {
    $this->path = $path;

    return $this;
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
   * @return string
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
   * @return mixed
   */
  public function getEnabled() {
    return $this->enabled;
  }

  /**
   * @param mixed $enabled
   * @return ScreenTemplate
   */
  public function setEnabled($enabled) {
    $this->enabled = $enabled;

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
   * Add screen
   *
   * @param \Indholdskanalen\MainBundle\Entity\Screen $screen
   * @return ScreenTemplate
   */
  public function addScreen(\Indholdskanalen\MainBundle\Entity\Screen $screen) {
    $this->screens[] = $screen;

    return $this;
  }

  /**
   * Remove screen
   *
   * @param \Indholdskanalen\MainBundle\Entity\Screen $screen
   * @return ScreenTemplate
   */
  public function removeScreen(\Indholdskanalen\MainBundle\Entity\Screen $screen) {
    $this->screens->removeElement($screen);

    return $this;
  }

  /**
   * Get screens
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getScreens() {
    return $this->screens;
  }

  /**
   * Set tools
   *
   * @param array $tools
   * @return ScreenTemplate
   */
  public function setTools(array $tools) {
    $this->orientation = serialize($tools);

    return $this;
  }

  /**
   * Get tools
   *
   * @return array
   */
  public function getTools() {
    return unserialize($this->orientation);
  }
}
