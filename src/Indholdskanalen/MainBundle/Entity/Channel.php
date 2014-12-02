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
 * Extra
 *
 * @AccessorOrder("custom", custom = {"id", "title" ,"orientation", "created_at", "slides"})
 *
 * @ORM\Table(name="ik_channel")
 * @ORM\Entity
 */
class Channel {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $id;

  /**
   * @ORM\Column(name="title", type="text", nullable=false)
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $title;

  /**
   * @ORM\Column(name="orientation", type="string", nullable=true)
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $orientation;

  /**
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   * @Groups({"api", "api-bulk", "search", "sharing"})
   */
  private $created_at;

  /**
   * @ORM\OneToMany(targetEntity="ChannelSlideOrder", mappedBy="channel", orphanRemoval=true)
   * @ORM\OrderBy({"sortOrder" = "ASC"})
   **/
  private $channelSlideOrders;

  /**
   * @ORM\ManyToMany(targetEntity="Screen", inversedBy="channels")
   * @ORM\JoinTable(name="ik_screens_channels")
   * @Groups({"api"})
   */
  private $screens;

  /**
   * @ORM\Column(name="user", type="integer", nullable=true)
   * @Groups({"api", "search"})
   */
  private $user;

  /**
   * @ORM\Column(name="modified_at", type="integer", nullable=false)
   */
  private $modified_at;

  /**
   * @ORM\ManyToMany(targetEntity="SharingIndex", mappedBy="channels")
   * @Groups({"api"})
   */
  private $sharingIndexes;

  /**
   * @ORM\Column(name="sharing_id", type="string", nullable=false)
   * @Groups({"sharing", "api"})
   */
  private $sharingId;

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
   * Set sharingId
   *
   * @param string $sharingId
   */
  public function setSharingId($sharingId) {
    $this->sharingId = $sharingId;
  }

  /**
   * Get sharingId
   *
   * @return string
   */
  public function getSharingId() {
    return $this->sharingId;
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
   * Set created_at
   *
   * @param integer $createdAt
   * @return Channel
   */
  public function setCreatedAt($createdAt) {
    $this->created_at = $createdAt;

    return $this;
  }

  /**
   * Get created_at
   *
   * @return integer
   */
  public function getCreatedAt() {
    return $this->created_at;
  }

  /**
   * Add screen
   *
   * @param \Indholdskanalen\MainBundle\Entity\Screen $screen
   * @return Channel
   */
  public function addScreen(\Indholdskanalen\MainBundle\Entity\Screen $screen) {
    $this->screens[] = $screen;

    return $this;
  }

  /**
   * Remove screen
   *
   * @param \Indholdskanalen\MainBundle\Entity\Screen $screen
   */
  public function removeScreen(\Indholdskanalen\MainBundle\Entity\Screen $screen) {
    $this->screens->removeElement($screen);
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
    foreach($slideorders as $slideorder) {
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
    $criteria = Criteria::create()->orderBy(array("sortOrder" => Criteria::ASC));

    $slideorders = $this->getChannelSlideOrders()->matching($criteria);
    foreach($slideorders as $slideorder) {
      $slide = $slideorder->getSlide();
      if($slide->isSlideActive()) {
        $result->add($slide);
      }
    }

    return $result;
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
   * Set modified_at
   *
   * @param integer $modifiedAt
   * @return Channel
   */
  public function setModifiedAt($modifiedAt) {
    $this->modified_at = $modifiedAt;

    return $this;
  }

  /**
   * Get modified_at
   *
   * @return integer
   */
  public function getModifiedAt() {
    return $this->modified_at;
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
}
