<?php
/**
 * @file
 * ScreenChannelRegion model.
 */

namespace Os2Display\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Mapping class between channel and screen regions.
 *
 * @ORM\Table(name="ik_channel_screen_regions")
 * @ORM\Entity
 */
class ChannelScreenRegion {
  /**
   * Id.
   *
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api"})
   */
  protected $id;

  /**
   * Ordering number.
   *
   * @ORM\Column(name="sort_order", type="integer")
   * @Groups({"api"})
   */
  protected $sortOrder;

  /**
   * Region in screen to include channel in.
   *
   * @ORM\Column(name="region", type="integer")
   * @Groups({"api"})
   */
  protected $region;

  /**
   * Screen to add channel to.
   *
   * @ORM\ManyToOne(targetEntity="Screen", inversedBy="channelScreenRegions")
   * @ORM\JoinColumn(name="screen_id", referencedColumnName="id")
   * @Groups({"api"})
   */
  protected $screen;

  /**
   * Channel to add.
   *
   * @ORM\ManyToOne(targetEntity="Channel", inversedBy="channelScreenRegions")
   * @ORM\JoinColumn(name="channel_id", referencedColumnName="id", nullable=true)
   * @Groups({"api"})
   */
  protected $channel;

  /**
   * Channel to add.
   *
   * @ORM\ManyToOne(targetEntity="SharedChannel", inversedBy="channelScreenRegions")
   * @ORM\JoinColumn(name="shared_channel_id", referencedColumnName="id", nullable=true)
   * @Groups({"api"})
   */
  protected $sharedChannel;

  /**
   * Constructor.
   *
   * Set default values.
   */
  public function __construct() {
    $this->sortOrder = 0;
    $this->region = 1;
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
   * Set sortOrder
   *
   * @param integer $sortOrder
   * @return ChannelScreenRegion
   */
  public function setSortOrder($sortOrder) {
    $this->sortOrder = $sortOrder;

    return $this;
  }

  /**
   * Get sortOrder
   *
   * @return integer
   */
  public function getSortOrder() {
    return $this->sortOrder;
  }

  /**
   * Set region
   *
   * @param integer $region
   * @return ChannelScreenRegion
   */
  public function setRegion($region) {
    $this->region = $region;

    return $this;
  }

  /**
   * Get region
   *
   * @return integer
   */
  public function getRegion() {
    return $this->region;
  }

  /**
   * Set screen
   *
   * @param null|\Os2Display\CoreBundle\Entity\Screen $screen
   * @return ChannelScreenRegion
   */
  public function setScreen(\Os2Display\CoreBundle\Entity\Screen $screen = NULL) {
    $this->screen = $screen;

    return $this;
  }

  /**
   * Get screen
   *
   * @return null|\Os2Display\CoreBundle\Entity\Screen
   */
  public function getScreen() {
    return $this->screen;
  }

  /**
   * Set channel
   *
   * @param null|\Os2Display\CoreBundle\Entity\Channel $channel
   * @return ChannelScreenRegion
   */
  public function setChannel(\Os2Display\CoreBundle\Entity\Channel $channel = NULL) {
    $this->channel = $channel;

    return $this;
  }

  /**
   * Get channel
   *
   * @return null|\Os2Display\CoreBundle\Entity\Channel
   */
  public function getChannel() {
    return $this->channel;
  }

  /**
   * Set sharedChannel
   *
   * @param null|\Os2Display\CoreBundle\Entity\SharedChannel $sharedChannel
   * @return ChannelScreenRegion
   */
  public function setSharedChannel(\Os2Display\CoreBundle\Entity\SharedChannel $sharedChannel = NULL) {
    $this->sharedChannel = $sharedChannel;

    return $this;
  }

  /**
   * Get sharedChannel
   *
   * @return null|\Os2Display\CoreBundle\Entity\SharedChannel
   */
  public function getSharedChannel() {
    return $this->sharedChannel;
  }
}
