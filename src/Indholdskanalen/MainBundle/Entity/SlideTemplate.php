<?php
/**
 * @file
 * Slide template.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Slide template entity.
 *
 * @ORM\Table(name="ik_slide_templates")
 * @ORM\Entity
 */
class SlideTemplate {
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
   * @ORM\Column(name="enabled", type="boolean")
   * @Groups({"api", "api-bulk"})
   */
  protected $enabled;

  /**
   * @ORM\Column(name="path_icon", type="string")
   */
  protected $pathIcon;

  /**
   * @ORM\Column(name="path_preview", type="string")
   */
  protected $pathPreview;

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
   * @ORM\Column(name="orientation", type="string")
   * @Groups({"api", "api-bulk"})
   */
  protected $orientation;

  /**
   * @ORM\Column(name="ideal_dimensions", type="json_array")
   * @Groups({"api", "api-bulk"})
   */
  protected $idealDimensions;

  /**
   * @ORM\Column(name="media_type", type="string")
   * @Groups({"api", "api-bulk"})
   */
  protected $mediaType;

  /**
   * @ORM\Column(name="slide_type", type="string", nullable=true))
   * @Groups({"api", "api-bulk"})
   */
  protected $slideType;

  /**
   * @ORM\Column(name="empty_options", type="json_array")
   * @Groups({"api", "api-bulk"})
   */
  protected $emptyOptions;

  /**
   * Get the paths virtual property.
   *
   * @return array
   *
   * @VirtualProperty
   * @SerializedName("paths")
   * @Groups({"api", "api-bulk"})
   */
  public function getPaths() {
    $result = array(
      'icon' => $this->getPathIcon(),
      'live' => $this->getPathLive(),
      'edit' => $this->getPathEdit(),
      'css' => $this->getPathCss(),
      'path' => $this->getPath(),
      'preview' => $this->getPathPreview(),
    );
    return $result;
  }

  /**
   * @return mixed
   */
  public function getEmptyOptions() {
    return $this->emptyOptions;
  }

  /**
   * @param mixed $emptyOptions
   */
  public function setEmptyOptions($emptyOptions) {
    $this->emptyOptions = $emptyOptions;
  }

  /**
   * @return mixed
   */
  public function getIdealDimensions() {
    return $this->idealDimensions;
  }

  /**
   * @param mixed $idealDimensions
   */
  public function setIdealDimensions($idealDimensions) {
    $this->idealDimensions = $idealDimensions;
  }

  /**
   * @return mixed
   */
  public function getMediaType() {
    return $this->mediaType;
  }

  /**
   * @param mixed $mediaType
   */
  public function setMediaType($mediaType) {
    $this->mediaType = $mediaType;
  }

  /**
   * @return mixed
   */
  public function getOrientation() {
    return $this->orientation;
  }

  /**
   * @param mixed $orientation
   */
  public function setOrientation($orientation) {
    $this->orientation = $orientation;
  }

  /**
   * @return mixed
   */
  public function getPathPreview() {
    return $this->pathPreview;
  }

  /**
   * @param mixed $pathPreview
   */
  public function setPathPreview($pathPreview) {
    $this->pathPreview = $pathPreview;
  }

  /**
   * @return mixed
   */
  public function getSlideType() {
    return $this->slideType;
  }

  /**
   * @param mixed $slideType
   */
  public function setSlideType($slideType) {
    $this->slideType = $slideType;
  }

  /**
   * @return mixed
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * @param mixed $path
   */
  public function setPath($path) {
    $this->path = $path;
  }

  /**
   * @return mixed
   */
  public function getPathCss() {
    return $this->pathCss;
  }

  /**
   * @param mixed $pathCss
   */
  public function setPathCss($pathCss) {
    $this->pathCss = $pathCss;
  }

  /**
   * @return mixed
   */
  public function getPathEdit() {
    return $this->pathEdit;
  }

  /**
   * @param mixed $pathEdit
   */
  public function setPathEdit($pathEdit) {
    $this->pathEdit = $pathEdit;
  }

  /**
   * @return mixed
   */
  public function getPathIcon() {
    return $this->pathIcon;
  }

  /**
   * @param mixed $pathIcon
   */
  public function setPathIcon($pathIcon) {
    $this->pathIcon = $pathIcon;
  }

  /**
   * @return mixed
   */
  public function getPathLive() {
    return $this->pathLive;
  }

  /**
   * @param mixed $pathLive
   */
  public function setPathLive($pathLive) {
    $this->pathLive = $pathLive;
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
   * Get name
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return mixed
   */
  public function getEnabled() {
    return $this->enabled;
  }

  /**
   * @param mixed $enabled
   */
  public function setEnabled($enabled) {
    $this->enabled = $enabled;
  }
}
