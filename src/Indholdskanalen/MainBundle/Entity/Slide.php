<?php
/**
 * @file
 * Slide model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

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
   * @Groups({"api", "search", "sharing", "middleware"})
   */
  private $id;

  /**
   * @ORM\Column(name="title", type="text", nullable=false)
   * @Groups({"api", "search", "sharing", "middleware"})
   */
  private $title;

  /**
   * @ORM\Column(name="orientation", type="string", nullable=true)
   * @Groups({"api", "search", "sharing", "middleware"})
   */
  private $orientation;

  /**
   * @ORM\Column(name="template", type="string", nullable=true)
   * @Groups({"api", "middleware"})
   */
  private $template;

  /**
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   * @Groups({"api", "search", "sharing"})
   */
  private $created_at;

  /**
   * @ORM\Column(name="options", type="json_array", nullable=true)
   * @Groups({"api", "search", "sharing", "middleware"})
   */
  private $options;

  /**
   * @ORM\Column(name="user", type="integer", nullable=true)
   * @Groups({"api", "search"})
   */
  private $user;

  /**
   * @ORM\Column(name="duration", type="integer", nullable=true)
   * @Groups({"api", "middleware"})
   */
  private $duration;

  /**
   * @ORM\Column(name="schedule_from", type="integer", nullable=true)
   * @Groups({"api", "middleware"})
   */
  private $schedule_from;

  /**
   * @ORM\Column(name="schedule_to", type="integer", nullable=true)
   * @Groups({"api", "middleware"})
   */
  private $schedule_to;

  /**
   * @ORM\Column(name="published", type="boolean", nullable=true)
   * @Groups({"api", "middleware"})
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
   *   "video" or "image".
   * @Groups({"api", "middleware"})
   */
  private $mediaType;

  /**
   * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", inversedBy="logoSlides")
   * @ORM\JoinColumn(name="logo_id", referencedColumnName="id")
   * @Groups({"api"})
   */
  private $logo;

  /**
   * Constructor
   */
  public function __construct()
  {
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
   * @VirtualProperty
   * @SerializedName("options")
   * @Groups({"middleware"})
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

  /**
   * Add channelSlideOrder
   *
   * @param \Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder
   * @return Slide
   */
  public function addChannelSlideOrder(\Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder)
  {
    $this->channelSlideOrders[] = $channelSlideOrder;

    return $this;
  }

  /**
   * Remove channelSlideOrder
   *
   * @param \Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder
   */
  public function removeChannelSlideOrder(\Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder)
  {
    $this->channelSlideOrders->removeElement($channelSlideOrder);
  }

  /**
   * Get channelSlideOrder
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getChannelSlideOrders()
  {
    return $this->channelSlideOrders;
  }

  /**
   * Set published
   *
   * @param boolean $published
   * @return Slide
   */
  public function setPublished($published)
  {
    $this->published = $published;

    return $this;
  }

  /**
   * Get published
   *
   * @return boolean
   */
  public function getPublished()
  {
    return $this->published;
  }

  /**
   * Set schedule_from
   *
   * @param integer $scheduleFrom
   * @return Slide
   */
  public function setScheduleFrom($scheduleFrom)
  {
    $this->schedule_from = $scheduleFrom;

    return $this;
  }

  /**
   * Get schedule_from
   *
   * @return integer
   */
  public function getScheduleFrom()
  {
    return $this->schedule_from;
  }

  /**
   * Set schedule_to
   *
   * @param integer $scheduleTo
   * @return Slide
   */
  public function setScheduleTo($scheduleTo)
  {
    $this->schedule_to = $scheduleTo;

    return $this;
  }

  /**
   * Get schedule_to
   *
   * @return integer
   */
  public function getScheduleTo()
  {
    return $this->schedule_to;
  }

  /**
   * Add mediaOrder
   *
   * @param \Indholdskanalen\MainBundle\Entity\MediaOrder $mediaOrder
   * @return Slide
   */
  public function addMediaOrder(\Indholdskanalen\MainBundle\Entity\MediaOrder $mediaOrder)
  {
    $this->mediaOrders[] = $mediaOrder;

    return $this;
  }

  /**
   * Remove mediaOrder
   *
   * @param \Indholdskanalen\MainBundle\Entity\MediaOrder $mediaOrder
   */
  public function removeMediaOrder(\Indholdskanalen\MainBundle\Entity\MediaOrder $mediaOrder)
  {
    $this->mediaOrders->removeElement($mediaOrder);
  }

  /**
   * Get mediaOrders
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getMediaOrders()
  {
    return $this->mediaOrders;
  }


  /**
   * Set mediaType
   *
   * @param string $mediaType
   * @return Slide
   */
  public function setMediaType($mediaType)
  {
    $this->mediaType = $mediaType;

    return $this;
  }

  /**
   * Get mediaType
   *
   * @return string
   */
  public function getMediaType()
  {
    return $this->mediaType;
  }

  /**
   * Get media
   *
   * @return \Doctrine\Common\Collections\Collection
   *
   * @VirtualProperty
   * @SerializedName("media")
   * @Groups({"api"})
   */
  public function getMedia()
  {
    $result = new ArrayCollection();
    foreach($this->getMediaOrders() as $mediaorder) {
      $result->add($mediaorder->getMedia());
    }
    return $result;
  }

	/**
	 * Is all media ready
	 *
	 * @return boolean
	 */
	public function getIsMediaReady()
	{
		$result = true;
		foreach($this->getMediaOrders() as $mediaorder) {
			$media = $mediaorder->getMedia();
			if($media->getProviderStatus() !== 1) {
				$result = false;
			}

		}
		return $result;
	}

  /**
   * Get channels
   *
   * @return \Doctrine\Common\Collections\Collection
   *
   * @VirtualProperty
   * @SerializedName("channels")
   * @Groups({"api"})
   */
  public function getChannels()
  {
    $result = new ArrayCollection();
    foreach($this->getChannelSlideOrders() as $channelorder) {
      $result->add($channelorder->getChannel());
    }
    return $result;
  }

  /**
   * Get logo
   *
   * @return mixed
   */
  public function getLogo()
  {
    return $this->logo;
  }

  /**
   * Set logo
   *
   * @param $logo
   * @return $this
   */
  public function setLogo($logo)
  {
    $this->logo = $logo;

    return $this;
  }
}
