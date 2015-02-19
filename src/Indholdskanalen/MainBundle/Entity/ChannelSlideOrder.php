<?php
/**
 * @file
 * ChannelSlideOrder model.
 */

namespace Indholdskanalen\MainBundle\Entity;

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
   * @param \Indholdskanalen\MainBundle\Entity\Slide $slide
   * @return ChannelSlideOrder
   */
  public function setSlide(\Indholdskanalen\MainBundle\Entity\Slide $slide = NULL) {
    $this->slide = $slide;

    return $this;
  }

  /**
   * Get slide
   *
   * @return \Indholdskanalen\MainBundle\Entity\Slide
   */
  public function getSlide() {
    return $this->slide;
  }

  /**
   * Set channel
   *
   * @param \Indholdskanalen\MainBundle\Entity\Channel $channel
   * @return ChannelSlideOrder
   */
  public function setChannel(\Indholdskanalen\MainBundle\Entity\Channel $channel = NULL) {
    $this->channel = $channel;

    return $this;
  }

  /**
   * Get channel
   *
   * @return \Indholdskanalen\MainBundle\Entity\Channel
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
