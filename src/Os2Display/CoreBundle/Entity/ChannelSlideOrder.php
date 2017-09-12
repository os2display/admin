<?php
/**
 * @file
 * ChannelSlideOrder model.
 */

namespace Os2Display\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 *
 *
 * @ORM\Table(name="ik_channelslideorder")
 * @ORM\Entity
 */
class ChannelSlideOrder {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api"})
   */
  private $id;

  /**
   * @ORM\Column(name="sort_order", type="integer")
   * @Groups({"api"})
   */
  private $sortOrder;

  /**
   * @ORM\ManyToOne(targetEntity="Slide", inversedBy="channelSlideOrders")
   * @ORM\JoinColumn(name="slide_id", referencedColumnName="id")
   * @Groups({"api"})
   */
  private $slide;

  /**
   * @ORM\ManyToOne(targetEntity="Channel", inversedBy="channelSlideOrders")
   * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
   * @Groups({"api"})
   */
  private $channel;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set slide
   *
   * @param null|\Os2Display\CoreBundle\Entity\Slide $slide
   * @return ChannelSlideOrder
   */
  public function setSlide(\Os2Display\CoreBundle\Entity\Slide $slide = NULL) {
    $this->slide = $slide;

    return $this;
  }

  /**
   * Get slide
   *
   * @return null|\Os2Display\CoreBundle\Entity\Slide
   */
  public function getSlide() {
    return $this->slide;
  }

  /**
   * Set channel
   *
   * @param null|\Os2Display\CoreBundle\Entity\Channel $channel
   * @return ChannelSlideOrder
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
   * Set sortOrder
   *
   * @param integer $sortOrder
   * @return ChannelSlideOrder
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
}
