<?php
/**
 * @file
 * Channel model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;

/**
 * Extra
 *
 * @ORM\Table(name="channel")
 * @ORM\Entity
 */
class Channel {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api"})
   */
  private $id;

  /**
   * @ORM\Column(name="title", type="text", nullable=false)
   * @Groups({"api"})
   */
  private $title;

  /**
   * @ORM\Column(name="orientation", type="string", nullable=true)
   * @Groups({"api"})
   */
  private $orientation;

  /**
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   * @Groups({"api"})
   */
  private $created_at;

  /**
   * @ORM\OneToMany(targetEntity="ChannelSlideOrder", mappedBy="channel")
   * @ORM\OrderBy({"sortOrder" = "ASC"})
   * @Groups({"api"})
   **/
  private $channelSlideOrders;

  /**
   * @ORM\ManyToMany(targetEntity="Screen", inversedBy="channels")
   * @ORM\JoinTable(name="screens_channels")
   * @Groups({"api"})
   */
  private $screens;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->screens = new ArrayCollection();
    $this->channelSlideOrders = new ArrayCollection();
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
   * Set created_at
   *
   * @param integer $createdAt
   * @return Channel
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
}
