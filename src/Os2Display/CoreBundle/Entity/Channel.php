<?php
/**
 * @file
 * Contains the Channel model.
 */

namespace Os2Display\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Os2Display\CoreBundle\Traits\Groupable;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Channel entity.
 *
 * @AccessorOrder("custom", custom = {"id", "title" ,"orientation", "created_at", "slides"})
 *
 * @ORM\Table(name="ik_channel")
 * @ORM\Entity
 */
class Channel extends ApiEntity implements GroupableEntity {
  use Groupable;

  /**
   * Id.
   *
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $id;

  /**
   * Title.
   *
   * @ORM\Column(name="title", type="text", nullable=false)
   * @Groups({"api", "api-bulk", "search", "sharing", "middleware"})
   */
  private $title;

  /**
   * Creation timestamp.
   *
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $createdAt;

  /**
   * Order of slides in channel.
   *
   * @ORM\OneToMany(targetEntity="ChannelSlideOrder", mappedBy="channel", orphanRemoval=true)
   * @ORM\OrderBy({"sortOrder" = "ASC"})
   **/
  private $channelSlideOrders;

  /**
   * Mappings between channel and screens/regions.
   *
   * @ORM\OneToMany(targetEntity="ChannelScreenRegion", mappedBy="channel", orphanRemoval=true)
   * @ORM\OrderBy({"sortOrder" = "ASC"})
   * @Groups({"api"})
   */
  private $channelScreenRegions;

  /**
   * User that created the channel.
   *
   * @ORM\Column(name="user", type="integer", nullable=true)
   * @Groups({"api", "search"})
   */
  private $user;

  /**
   * Last modified time.
   *
   * @ORM\Column(name="modified_at", type="integer", nullable=false)
   */
  private $modifiedAt;

  /**
   * Indexes the channel are shared in?
   *
   * @ORM\ManyToMany(targetEntity="SharingIndex", mappedBy="channels")
   * @Groups({"api"})
   */
  private $sharingIndexes;

  /**
   * Unique id across all installations connected to the sharing service.
   *
   * @ORM\Column(name="unique_id", type="string", nullable=true)
   * @Groups({"sharing", "api"})
   */
  private $uniqueId;

  /**
   * Hash of the last pushed data regarding the screen.
   *
   * Used to determine if the channel should be pushed again.
   *
   * @ORM\Column(name="last_push_hash", type="string", nullable=true)
   */
  private $lastPushHash;

  /**
   * Which screens was the channel last pushed to?
   *
   * Used to determine if the channel should be pushed again.
   *
   * @ORM\Column(name="last_push_screens", type="json_array", nullable=true)
   */
  private $lastPushScreens;

  /**
   * @ORM\Column(name="publish_from", type="integer", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $publishFrom;

  /**
   * @ORM\Column(name="publish_to", type="integer", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $publishTo;

  /**
   * @ORM\Column(name="schedule_repeat", type="boolean", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $scheduleRepeat;

  /**
   * @ORM\Column(name="schedule_repeat_from", type="integer", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $scheduleRepeatFrom;

  /**
   * @ORM\Column(name="schedule_repeat_to", type="integer", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $scheduleRepeatTo;

  /**
   * @ORM\Column(name="schedule_repeat_days", type="json_array", nullable=true)
   * @Groups({"api", "api-bulk", "middleware", "sharing"})
   */
  private $scheduleRepeatDays;

  /**
   * Constructor
   */
  public function __construct() {
    $this->channelSlideOrders = new ArrayCollection();
    $this->sharingIndexes = new ArrayCollection();
    $this->lastPushScreens = json_encode(array());
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
   * Set lastPushHash
   *
   * @param string $lastPushHash
   * @return Channel
   */
  public function setLastPushHash($lastPushHash) {
    $this->lastPushHash = $lastPushHash;

    return $this;
  }

  /**
   * Get lastPushHash
   *
   * @return string
   */
  public function getLastPushHash() {
    return $this->lastPushHash;
  }


  /**
   * Set lastPushScreens
   *
   * @param string $lastPushScreens
   * @return Channel
   */
  public function setLastPushScreens($lastPushScreens) {
    $this->lastPushScreens = $lastPushScreens;

    return $this;
  }

  /**
   * Get lastPushScreens
   *
   * @return string
   */
  public function getLastPushScreens() {
    return $this->lastPushScreens;
  }

  /**
   * Set lastPushTime
   *
   * @param integer $lastPushTime
   * @return Channel
   */
  public function setLastPushTime($lastPushTime) {
    $this->lastPushTime = $lastPushTime;

    return $this;
  }

  /**
   * Get lastPushTime
   *
   * @return integer
   */
  public function getLastPushTime() {
    return $this->lastPushTime;
  }

  /**
   * Set title
   *
   * @param string $title
   *
   * @return Channel
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
   * Set uniqueId
   *
   * @param string $uniqueId
   *
   * @return Channel
   */
  public function setUniqueId($uniqueId) {
    $this->uniqueId = $uniqueId;

    return $this;
  }

  /**
   * Get uniqueId
   *
   * @return string
   */
  public function getUniqueId() {
    return $this->uniqueId;
  }

  /**
   * Set createdAt
   *
   * @param integer $createdAt
   * @return Channel
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
   * Add channelSlideOrder
   *
   * @param \Os2Display\CoreBundle\Entity\ChannelSlideOrder $channelSlideOrder
   * @return Channel
   */
  public function addChannelSlideOrder(\Os2Display\CoreBundle\Entity\ChannelSlideOrder $channelSlideOrder) {
    $this->channelSlideOrders[] = $channelSlideOrder;

    return $this;
  }

  /**
   * Remove channelSlideOrder
   *
   * @param \Os2Display\CoreBundle\Entity\ChannelSlideOrder $channelSlideOrder
   * @return Channel
   */
  public function removeChannelSlideOrder(\Os2Display\CoreBundle\Entity\ChannelSlideOrder $channelSlideOrder) {
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
   * Get all slides
   *
   * @return \Doctrine\Common\Collections\Collection
   *
   * @VirtualProperty
   * @SerializedName("slides")
   * @Groups({"api"})
   */
  public function getAllSlides() {
    $result = new ArrayCollection();
    $slideOrders = $this->getChannelSlideOrders();
    foreach ($slideOrders as $slideOrder) {
      $result->add($slideOrder->getSlide());
    }
    return $result;
  }

  /**
   * Get all published slides
   *
   * @return \Doctrine\Common\Collections\Collection
   *
   * @VirtualProperty
   * @SerializedName("slides")
   * @Groups({"api-bulk", "sharing"})
   */
  public function getPublishedSlides() {
    $result = new ArrayCollection();
    $criteria = Criteria::create()
      ->orderBy(array('sortOrder' => Criteria::ASC));

    $slideOrders = $this->getChannelSlideOrders()->matching($criteria);
    foreach ($slideOrders as $slideOrder) {
      $slide = $slideOrder->getSlide();
      if ($slide->isSlideActive()) {
        $result->add($slide);
      }
    }

    return $result;
  }

  /**
   * Get channel content.
   *
   * @return array
   *
   * @VirtualProperty
   * @SerializedName("data")
   * @Groups({"middleware"})
   */
  public function getData() {
    return array(
      'id' => $this->getId(),
      'slides' => $this->getPublishedSlides()->toArray(),
      'publish_from' => $this->getPublishFrom(),
      'publish_to' => $this->getPublishTo(),
      'schedule_repeat' => $this->getScheduleRepeat(),
      'schedule_repeat_from' => $this->getScheduleRepeatFrom(),
      'schedule_repeat_to' => $this->getScheduleRepeatTo(),
      'schedule_repeat_days' => $this->getScheduleRepeatDays(),
    );
  }

  /**
   * Get channel content.
   *
   * @return array
   *
   * @VirtualProperty
   * @SerializedName("screens")
   * @Groups({"middleware"})
   */
  public function getMiddlewareScreens() {
    $screens = array();
    foreach ($this->getChannelScreenRegions() as $region) {
      if (!in_array($region->getScreen()->getId(), $screens)) {
        $screens[] = $region->getScreen()->getId();
      }
    }
    return $screens;
  }

  /**
   * Get regions.
   *
   * @return array
   *
   * @VirtualProperty
   * @SerializedName("regions")
   * @Groups({"middleware"})
   */
  public function getMiddlewareChannelScreenRegions() {
    $regions = array();
    foreach ($this->getChannelScreenRegions() as $region) {
      $regions[] = array(
        'screen' => $region->getScreen()->getId(),
        'region' => $region->getRegion()
      );
    }
    return $regions;
  }

  /**
   * Set user
   *
   * @param integer $user
   * @return Channel
   */
  public function setUser($user) {
    $this->user = $user;

    return $this;
  }

  /**
   * Get user
   *
   * @return integer
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * Set modifiedAt
   *
   * @param integer $modifiedAt
   * @return Channel
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

  /**
   * Add SharingIndex
   *
   * @param \Os2Display\CoreBundle\Entity\SharingIndex $sharingIndex
   * @return Channel
   */
  public function addSharingIndex(\Os2Display\CoreBundle\Entity\SharingIndex $sharingIndex) {
    $sharingIndex->addChannel($this);
    $this->sharingIndexes[] = $sharingIndex;

    return $this;
  }

  /**
   * Remove SharingIndex
   *
   * @param \Os2Display\CoreBundle\Entity\SharingIndex $sharingIndex
   * @return Channel
   */
  public function removeSharingIndex(\Os2Display\CoreBundle\Entity\SharingIndex $sharingIndex) {
    $sharingIndex->removeChannel($this);
    $this->sharingIndexes->removeElement($sharingIndex);

    return $this;
  }

  /**
   * Get SharingIndexes
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSharingIndexes() {
    return $this->sharingIndexes;
  }


  /**
   * Add channelScreenRegion
   *
   * @param \Os2Display\CoreBundle\Entity\ChannelScreenRegion $channelScreenRegion
   * @return Channel
   */
  public function addChannelScreenRegion(\Os2Display\CoreBundle\Entity\ChannelScreenRegion $channelScreenRegion) {
    $this->channelScreenRegions[] = $channelScreenRegion;

    return $this;
  }

  /**
   * Remove channelScreenRegion
   *
   * @param \Os2Display\CoreBundle\Entity\ChannelScreenRegion $channelScreenRegion
   * @return Channel
   */
  public function removeChannelScreenRegion(\Os2Display\CoreBundle\Entity\ChannelScreenRegion $channelScreenRegion) {
    $this->channelScreenRegions->removeElement($channelScreenRegion);

    return $this;
  }

  /**
   * Get channelScreenRegion
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getChannelScreenRegions() {
    return $this->channelScreenRegions;
  }

  /**
   * Get screens
   *
   * @return array
   */
  public function getScreens() {
    $screens = array();

    foreach ($this->getChannelScreenRegions() as $region) {
      $screens[] = $region->getScreen();
    }

    return $screens;
  }

  /**
   * @return mixed
   */
  public function getPublishFrom() {
    return $this->publishFrom;
  }

  /**
   * @param mixed $publishFrom
   *
   * @return Channel
   */
  public function setPublishFrom($publishFrom) {
    $this->publishFrom = $publishFrom;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getPublishTo() {
    return $this->publishTo;
  }

  /**
   * @param mixed $publishTo
   *
   * @return Channel
   */
  public function setPublishTo($publishTo) {
    $this->publishTo = $publishTo;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getScheduleRepeat() {
    return $this->scheduleRepeat;
  }

  /**
   * @param mixed $scheduleRepeat
   *
   * @return Channel
   */
  public function setScheduleRepeat($scheduleRepeat) {
    $this->scheduleRepeat = $scheduleRepeat;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getScheduleRepeatDays() {
    return $this->scheduleRepeatDays;
  }

  /**
   * @param mixed $scheduleRepeatDays
   *
   * @return Channel
   */
  public function setScheduleRepeatDays($scheduleRepeatDays) {
    $this->scheduleRepeatDays = $scheduleRepeatDays;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getScheduleRepeatFrom() {
    return $this->scheduleRepeatFrom;
  }

  /**
   * @param mixed $scheduleRepeatFrom
   *
   * @return Channel
   */
  public function setScheduleRepeatFrom($scheduleRepeatFrom) {
    $this->scheduleRepeatFrom = $scheduleRepeatFrom;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getScheduleRepeatTo() {
    return $this->scheduleRepeatTo;
  }

  /**
   * @param mixed $scheduleRepeatTo
   *
   * @return Channel
   */
  public function setScheduleRepeatTo($scheduleRepeatTo) {
    $this->scheduleRepeatTo = $scheduleRepeatTo;

    return $this;
  }
}
