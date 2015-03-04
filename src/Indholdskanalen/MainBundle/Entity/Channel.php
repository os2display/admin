<?php
/**
 * @file
 * Channel model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\MaxDepth;


/**
 * Channel entity.
 *
 * @AccessorOrder("custom", custom = {"id", "title" ,"orientation", "created_at", "slides"})
 *
 * @ORM\Table(name="ik_channel")
 * @ORM\Entity
 */
class Channel {
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
   * Orientation.
   *
   * landscape or portrait?
   *
   * @ORM\Column(name="orientation", type="string", nullable=true)
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $orientation;

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
   * When was the last time it was pushed?
   *
   * @ORM\Column(name="last_push_time", type="integer", nullable=true)
   */
  private $lastPushTime;

  /**
   * Constructor
   */
  public function __construct() {
    $this->screens = new ArrayCollection();
    $this->channelSlideOrders = new ArrayCollection();
    $this->sharingIndexes = new ArrayCollection();
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
   * @return Screen
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
   * @return Screen
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
   * Set uniqueId
   *
   * @param string $uniqueId
   */
  public function setUniqueId($uniqueId) {
    $this->uniqueId = $uniqueId;
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
   * @param \Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder
   * @return Channel
   */
  public function addChannelSlideOrder(\Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder) {
    $this->channelSlideOrders[] = $channelSlideOrder;

    return $this;
  }

  /**
   * Remove channelSlideOrder
   *
   * @param \Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder
   */
  public function removeChannelSlideOrder(\Indholdskanalen\MainBundle\Entity\ChannelSlideOrder $channelSlideOrder) {
    $this->channelSlideOrders->removeElement($channelSlideOrder);
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
    $slideorders = $this->getChannelSlideOrders();
    foreach ($slideorders as $slideorder) {
      $result->add($slideorder->getSlide());
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
      ->orderBy(array("sortOrder" => Criteria::ASC));

    $slideorders = $this->getChannelSlideOrders()->matching($criteria);
    foreach ($slideorders as $slideorder) {
      $slide = $slideorder->getSlide();
      if ($slide->isSlideActive()) {
        $result->add($slide);
      }
    }

    return $result;
  }

  /**
   * Get channel content.
   *
   * @return \array
   *
   * @VirtualProperty
   * @SerializedName("data")
   * @Groups({"middleware"})
   */
  public function getData() {
    return array(
      'id' => $this->getId(),
      'slides' => $this->getPublishedSlides()->toArray()
    );
  }

  /**
   * Get channel content.
   *
   * @return \array
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
   * @return \array
   *
   * @VirtualProperty
   * @SerializedName("regions")
   * @Groups({"middleware"})
   */
  public function getMiddlewareChannelScreenRegions() {
    $regions = array();
    foreach ($this->getChannelScreenRegions() as $region) {
      $regions[] = array(
        "screen" => $region->getScreen()->getId(),
        "region" => $region->getRegion()
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
   * @param \Indholdskanalen\MainBundle\Entity\SharingIndex $sharingIndex
   * @return Channel
   */
  public function addSharingIndex(\Indholdskanalen\MainBundle\Entity\SharingIndex $sharingIndex) {
    $sharingIndex->addChannel($this);
    $this->sharingIndexes[] = $sharingIndex;

    return $this;
  }

  /**
   * Remove SharingIndex
   *
   * @param \Indholdskanalen\MainBundle\Entity\SharingIndex $sharingIndex
   */
  public function removeSharingIndex(\Indholdskanalen\MainBundle\Entity\SharingIndex $sharingIndex) {
    $sharingIndex->removeChannel($this);
    $this->sharingIndexes->removeElement($sharingIndex);
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
   * @param \Indholdskanalen\MainBundle\Entity\ChannelScreenRegion $channelScreenRegion
   * @return Screen
   */
  public function addChannelScreenRegion(\Indholdskanalen\MainBundle\Entity\ChannelScreenRegion $channelScreenRegion) {
    $this->channelScreenRegions[] = $channelScreenRegion;

    return $this;
  }

  /**
   * Remove channelScreenRegion
   *
   * @param \Indholdskanalen\MainBundle\Entity\ChannelScreenRegion $channelScreenRegion
   */
  public function removeChannelScreenRegion(\Indholdskanalen\MainBundle\Entity\ChannelScreenRegion $channelScreenRegion) {
    $this->channelScreenRegions->removeElement($channelScreenRegion);
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
   */
  public function getScreens() {
    $screens = array();

    foreach($this->getChannelScreenRegions() as $region) {
      $screens[] = $region->getScreen();
    }

    return $screens;
  }
}
