<?php
/**
 * @file
 * Slide template.
 */

namespace Os2Display\CoreBundle\Entity;

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
   * @ORM\Column(name="path_js", type="string")
   * @Groups({"middleware"})
   */
  protected $pathJs;

  /**
   * @ORM\Column(name="path", type="string")
   * @Groups({"middleware"})
   */
  protected $path;

  /**
   * @ORM\Column(name="script_id", type="string")
   * @Groups({"middleware"})
   */
  protected $scriptId;

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
   * @ORM\Column(name="tools", type="json_array")
   * @Groups({"api", "api-bulk"})
   */
  protected $tools;

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
   *
   * @return SlideTemplate
   */
  public function setEmptyOptions($emptyOptions) {
    $this->emptyOptions = $emptyOptions;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getTools() {
    return $this->tools;
  }

  /**
   * @param mixed $tools
   *
   * @return SlideTemplate
   */
  public function setTools($tools) {
    $this->tools = $tools;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getIdealDimensions() {
    return $this->idealDimensions;
  }

  /**
   * @param mixed $idealDimensions
   *
   * @return SlideTemplate
   */
  public function setIdealDimensions($idealDimensions) {
    $this->idealDimensions = $idealDimensions;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getMediaType() {
    return $this->mediaType;
  }

  /**
   * @param mixed $mediaType
   *
   * @return SlideTemplate
   */
  public function setMediaType($mediaType) {
    $this->mediaType = $mediaType;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getOrientation() {
    return $this->orientation;
  }

  /**
   * @param mixed $orientation
   *
   * @return SlideTemplate
   */
  public function setOrientation($orientation) {
    $this->orientation = $orientation;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getPathPreview() {
    return $this->pathPreview;
  }

  /**
   * @param mixed $pathPreview
   *
   * @return SlideTemplate
   */
  public function setPathPreview($pathPreview) {
    $this->pathPreview = $pathPreview;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getSlideType() {
    return $this->slideType;
  }

  /**
   * @param mixed $slideType
   *
   * @return SlideTemplate
   */
  public function setSlideType($slideType) {
    $this->slideType = $slideType;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * @param mixed $path
   *
   * @return SlideTemplate
   */
  public function setPath($path) {
    $this->path = $path;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getPathCss() {
    return $this->pathCss;
  }

  /**
   * @param mixed $pathCss
   *
   * @return SlideTemplate
   */
  public function setPathCss($pathCss) {
    $this->pathCss = $pathCss;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getPathEdit() {
    return $this->pathEdit;
  }

  /**
   * @param mixed $pathEdit
   *
   * @return SlideTemplate
   */
  public function setPathEdit($pathEdit) {
    $this->pathEdit = $pathEdit;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getPathIcon() {
    return $this->pathIcon;
  }

  /**
   * @param mixed $pathIcon
   *
   * @return SlideTemplate
   */
  public function setPathIcon($pathIcon) {
    $this->pathIcon = $pathIcon;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getPathLive() {
    return $this->pathLive;
  }

  /**
   * @param mixed $pathLive
   *
   * @return SlideTemplate
   */
  public function setPathLive($pathLive) {
    $this->pathLive = $pathLive;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getPathJs() {
    return $this->pathJs;
  }

  /**
   * @param mixed $pathJs
   *
   * @return SlideTemplate
   */
  public function setPathJs($pathJs) {
    $this->pathJs = $pathJs;

    return $this;
  }


  /**
   * @return mixed
   */
  public function getScriptId() {
    return $this->scriptId;
  }

  /**
   * @param mixed $scriptId
   *
   * @return SlideTemplate
   */
  public function setScriptId($scriptId) {
    $this->scriptId = $scriptId;

    return $this;
  }

  /**
   * Set id
   *
   * @param string $id
   *
   * @return SlideTemplate
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
   * @return SlideTemplate
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
   *
   * @return SlideTemplate
   */
  public function setEnabled($enabled) {
    $this->enabled = $enabled;

    return $this;
  }
}
