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
 * Shared Channel - Channel that is loaded from another installation.
 *
 * @ORM\Table(name="ik_shared_channel")
 * @ORM\Entity
 */
class SharedChannel {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api", "api-bulk", "sharing"})
   */
  private $id;

  /**
   * @ORM\Column(name="unique_id", type="text", nullable=false)
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $uniqueId;

  /**
   * @ORM\Column(name="`index`", type="text", nullable=false)
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $index;

  /**
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $createdAt;

  /**
   * Mappings between channel and screens/regions.
   *
   * @ORM\OneToMany(targetEntity="ChannelScreenRegion", mappedBy="sharedChannel", orphanRemoval=true)
   * @ORM\OrderBy({"sortOrder" = "ASC"})
   * @Groups({"api"})
   */
  private $channelScreenRegions;

  /**
   * @ORM\Column(name="modified_at", type="integer", nullable=false)
   */
  private $modifiedAt;

  /**
   * @ORM\Column(name="content", type="text", nullable=true)
   * @Groups({"api"})
   */
  private $content;

  /**
   * @ORM\Column(name="last_push_hash", type="string", nullable=true)
   */
  private $lastPushHash;

  /**
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
   * @return SharedChannel
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
   * @return SharedChannel
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
   * @return SharedChannel
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
   * Set uniqueId
   *
   * @param string $uniqueId
   *
   * @return SharedChannel
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
   * Set index
   *
   * @param string $index
   *
   * @return SharedChannel
   */
  public function setIndex($index) {
    $this->index = $index;

    return $this;
  }

  /**
   * Get index
   *
   * @return string
   */
  public function getIndex() {
    return $this->index;
  }

  /**
   * Set createdAt
   *
   * @param integer $createdAt
   *
   * @return SharedChannel
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
   * Set modifiedAt
   *
   * @param integer $modifiedAt
   *
   * @return SharedChannel
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
   * Set content
   *
   * @param string $content
   *
   * @return SharedChannel
   */
  public function setContent($content) {
    $this->content = $content;

    return $this;
  }

  /**
   * Get content
   *
   * @return string
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * Get sharedChannel screens.
   *
   * @return array
   *
   * @VirtualProperty
   * @SerializedName("screens")
   * @Groups({"middleware"})
   */
  public function getMiddlewareScreens() {
    $slides = array();
    foreach ($this->getChannelScreenRegions() as $region) {
      if (!in_array($region->getScreen()->getId(), $slides)) {
        $slides[] = $region->getScreen()->getId();
      }
    }
    return $slides;
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
   * Get sharedChannel content.
   *
   * @return array
   *
   * @VirtualProperty
   * @SerializedName("data")
   * @Groups({"middleware"})
   */
  public function getData() {
    return array(
      'id' => $this->getUniqueId(),
      'slides' => json_encode(json_decode($this->content)->slides)
    );
  }

  /**
   * Get sharedChannel id.
   *
   * @return string
   *
   * @VirtualProperty
   * @SerializedName("id")
   * @Groups({"middleware"})
   */
  public function getMiddlewareId() {
    return $this->uniqueId;
  }

  /**
   * Add channelScreenRegion
   *
   * @param \Indholdskanalen\MainBundle\Entity\ChannelScreenRegion $channelScreenRegion
   * @return SharedChannel
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
   *
   * @return array
   */
  public function getScreens() {
    $screens = array();

    foreach($this->getChannelScreenRegions() as $region) {
      $screens[] = $region->getScreen();
    }

    return $screens;
  }
}
