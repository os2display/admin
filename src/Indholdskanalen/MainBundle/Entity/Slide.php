<?php
/**
 * @file
 * Slide model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Indholdskanalen\MainBundle\Traits\Groupable;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Extra
 *
 * @ORM\Table(name="ik_slide")
 * @ORM\Entity
 */
class Slide extends ApiEntity implements GroupableEntity {
  use Groupable;

  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api", "api-bulk", "search", "sharing", "middleware"})
   */
  private $id;

  /**
   * @ORM\Column(name="title", type="text", nullable=false)
   * @Groups({"api", "api-bulk", "search", "sharing", "middleware"})
   */
  private $title;

  /**
   * @ORM\Column(name="orientation", type="string", nullable=true)
   * @Groups({"api", "api-bulk", "search", "sharing", "middleware"})
   */
  private $orientation;

  /**
   * @ORM\Column(name="template", type="string", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $template;

  /**
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $createdAt;

  /**
   * @ORM\Column(name="options", type="json_array", nullable=true)
   * @Groups({"api", "api-bulk", "sharing", "middleware"})
   */
  private $options;

  /**
   * @ORM\Column(name="user", type="integer", nullable=true)
   * @Groups({"api", "api-bulk", "search"})
   */
  private $user;

  /**
   * @ORM\Column(name="duration", type="integer", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $duration;

  /**
   * @ORM\Column(name="schedule_from", type="integer", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $scheduleFrom;

  /**
   * @ORM\Column(name="schedule_to", type="integer", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $scheduleTo;

  /**
   * @ORM\Column(name="published", type="boolean", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $published;

  /**
   * @ORM\OneToMany(targetEntity="ChannelSlideOrder", mappedBy="slide", orphanRemoval=true)
   * @ORM\OrderBy({"sortOrder" = "ASC"})
   */
  private $channelSlideOrders;

  /**
   * @ORM\OneToMany(targetEntity="MediaOrder", mappedBy="slide", orphanRemoval=true)
   * @ORM\OrderBy({"sortOrder" = "ASC"})
   */
  private $mediaOrders;

  /**
   * @ORM\Column(name="media_type", type="string", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $mediaType;

  /**
   * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", inversedBy="logoSlides")
   * @ORM\JoinColumn(name="logo_id", referencedColumnName="id")
   * @Groups({"api", "api-bulk"})
   */
  private $logo;

  /**
   * @ORM\Column(name="modified_at", type="integer", nullable=false)
   */
  private $modifiedAt;

  /**
   * @ORM\Column(name="slide_type", type="string", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $slideType;

  /**
   * @Groups({"middleware"})
   *
   * @JMS\Type("array")
   * @ORM\Column(name="external_data", type="json_array", nullable=true)
   */
  protected $externalData;

  /**
   * Constructor
   */
  public function __construct() {
    $this->channelSlideOrders = new \Doctrine\Common\Collections\ArrayCollection();
    $this->mediaOrders = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Get externalData.
   *
   * @return mixed
   */
  public function getExternalData() {
    return $this->externalData;
  }

  /**
   * Set externalData.
   *
   * @param $externalData
   * @return Slide
   */
  public function setExternalData($externalData) {
    $this->externalData = $externalData;

    return $this;
  }

  /**
   * Set slideType
   *
   * @param string $slideType
   * @return Slide
   */
  public function setSlideType($slideType) {
    $this->slideType = $slideType;

    return $this;
  }

  /**
   * Get slideType
   *
   * @return string
   */
  public function getSlideType() {
    return $this->slideType;
  }

  /**
   * Set title
   *
   * @param string $title
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
   * @param string $orientation
   * @return Slide
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
   * Set template
   *
   * @param \string $template
   * @return Slide
   */
  public function setTemplate($template) {
    $this->template = $template;

    return $this;
  }

  /**
   * Get template
   *
   * @return \string
   * @VirtualProperty
   * @SerializedName("template")
   * @Groups({"middleware"})
   */

  public function getTemplate() {
    return $this->template;
  }

  /**
   * Set user
   *
   * @param string $user
   * @return Slide
   */
  public function setUser($user) {
    $this->user = $user;

    return $this;
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
   * @VirtualProperty
   * @SerializedName("options")
   * @Groups({"middleware"})
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * Set createdAt
   *
   * @param integer $createdAt
   * @return Slide
   */
  public function setCreatedAt($createdAt) {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * Get createdAt
   *
   * @return integer
   */
  public function getCreatedAt() {
    return $this->createdAt;
  }

  /**
   * Set duration
   *
   * @param integer $duration
   * @return Slide
   */
  public function setDuration($duration) {
    $this->duration = $duration;

    return $this;
  }

  /**
   * Get duration
   *
   * @return integer
   */
  public function getDuration() {
    return $this->duration;
  }

  /**
   * Add channelSlideOrder
   *
   * @param \Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder
   * @return Slide
   */
  public function addChannelSlideOrder(\Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder) {
    $this->channelSlideOrders[] = $channelSlideOrder;

    return $this;
  }

  /**
   * Remove channelSlideOrder
   *
   * @param \Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder
   * @return Slide
   */
  public function removeChannelSlideOrder(\Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder) {
    $this->channelSlideOrders->removeElement($channelSlideOrder);

    return $this;
  }

  /**
   * Get channelSlideOrder
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getChannelSlideOrders() {
    return $this->channelSlideOrders;
  }

  /**
   * Set published
   *
   * @param boolean $published
   * @return Slide
   */
  public function setPublished($published) {
    $this->published = $published;

    return $this;
  }

  /**
   * Get published
   *
   * @return boolean
   */
  public function getPublished() {
    return $this->published;
  }

  /**
   * Set scheduleFrom
   *
   * @param integer $scheduleFrom
   * @return Slide
   */
  public function setScheduleFrom($scheduleFrom) {
    $this->scheduleFrom = $scheduleFrom;

    return $this;
  }

  /**
   * Get scheduleFrom
   *
   * @return integer
   */
  public function getScheduleFrom() {
    return $this->scheduleFrom;
  }

  /**
   * Set scheduleTo
   *
   * @param integer $scheduleTo
   * @return Slide
   */
  public function setScheduleTo($scheduleTo) {
    $this->scheduleTo = $scheduleTo;

    return $this;
  }

  /**
   * Get scheduleTo
   *
   * @return integer
   */
  public function getScheduleTo() {
    return $this->scheduleTo;
  }

  /**
   * Add mediaOrder
   *
   * @param \Indholdskanalen\MainBundle\Entity\MediaOrder $mediaOrder
   * @return Slide
   */
  public function addMediaOrder(\Indholdskanalen\MainBundle\Entity\MediaOrder $mediaOrder) {
    $this->mediaOrders[] = $mediaOrder;

    return $this;
  }

  /**
   * Remove mediaOrder
   *
   * @param \Indholdskanalen\MainBundle\Entity\MediaOrder $mediaOrder
   * @return Slide
   */
  public function removeMediaOrder(\Indholdskanalen\MainBundle\Entity\MediaOrder $mediaOrder) {
    $this->mediaOrders->removeElement($mediaOrder);

    return $this;
  }

  /**
   * Get mediaOrders
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getMediaOrders() {
    return $this->mediaOrders;
  }


  /**
   * Set mediaType
   *
   * @param string $mediaType
   * @return Slide
   */
  public function setMediaType($mediaType) {
    $this->mediaType = $mediaType;

    return $this;
  }

  /**
   * Get mediaType
   *
   * @return string
   */
  public function getMediaType() {
    return $this->mediaType;
  }

  /**
   * Get media
   *
   * @return \Doctrine\Common\Collections\Collection
   *
   * @VirtualProperty
   * @SerializedName("media")
   * @Groups({"api", "api-bulk"})
   */
  public function getMedia() {
    $result = new ArrayCollection();
    foreach ($this->getMediaOrders() as $mediaorder) {
      $result->add($mediaorder->getMedia());
    }
    return $result;
  }

  /**
   * Is all media ready
   *
   * @return boolean
   */
  public function isMediaReady() {
    foreach ($this->getMediaOrders() as $mediaorder) {
      $media = $mediaorder->getMedia();
      if ($media->getProviderStatus() !== 1) {
        return FALSE;
      }

    }
    return TRUE;
  }

  /**
   * Is the Slide currently scheduled to be shown
   *
   * @return boolean
   */
  public function isSlideInSchedule() {
    $to = $this->getScheduleTo();

    return empty($to) || $to > time();
  }

  /**
   * Is the Slide ready and active
   *
   * @return boolean
   */
  public function isSlideActive() {
    return $this->getPublished() && $this->isSlideInSchedule() && $this->isMediaReady();
  }

  /**
   * Get channels
   *
   * @return \Doctrine\Common\Collections\Collection
   *
   * @VirtualProperty
   * @SerializedName("channels")
   * @Groups({"api"})
   * @MaxDepth(6)
   */
  public function getChannels() {
    $result = new ArrayCollection();
    foreach ($this->getChannelSlideOrders() as $channelorder) {
      $result->add($channelorder->getChannel());
    }
    return $result;
  }

  /**
   * Get logo
   *
   * @return mixed
   */
  public function getLogo() {
    return $this->logo;
  }

  /**
   * Set logo
   *
   * @param $logo
   * @return $this
   */
  public function setLogo($logo) {
    $this->logo = $logo;

    return $this;
  }

  /**
   * Set modifiedAt
   *
   * @param integer $modifiedAt
   * @return Slide
   */
  public function setModifiedAt($modifiedAt) {
    $this->modifiedAt = $modifiedAt;

    return $this;
  }

  /**
   * Get modifiedAt
   *
   * @return integer
   */
  public function getModifiedAt() {
    return $this->modifiedAt;
  }
}
