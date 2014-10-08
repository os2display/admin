<?php
/**
 * @file
 * ChannelSlideOrder model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Media Order.
 * Represents an ordered media element.
 *
 * @ORM\Table(name="media_order")
 * @ORM\Entity
 */
class MediaOrder {
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
   * @ORM\ManyToOne(targetEntity="Slide", inversedBy="slide_media_orders")
   * @ORM\JoinColumn(name="slide_id", referencedColumnName="id")
   * @Groups({"api"})
   **/
  private $slide;

  /**
   * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", inversedBy="slide_media_orders")
   * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
   * @Groups({"api"})
   **/
  private $media;

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
   * @return ChannelSlideOrder
   */
  public function setSortOrder($sortOrder)
  {
    $this->sortOrder = $sortOrder;

    return $this;
  }

  /**
   * Get sortOrder
   *
   * @return integer
   */
  public function getSortOrder()
  {
    return $this->sortOrder;
  }

  /**
   * Set slide
   *
   * @param \Indholdskanalen\MainBundle\Entity\Slide $slide
   * @return MediaOrder
   */
  public function setSlide(\Indholdskanalen\MainBundle\Entity\Slide $slide = null)
  {
    $this->slide = $slide;

    return $this;
  }

  /**
   * Get slide
   *
   * @return \Indholdskanalen\MainBundle\Entity\Slide
   */
  public function getSlide()
  {
    return $this->slide;
  }

  /**
   * Set media
   *
   * @param \Application\Sonata\MediaBundle\Entity\Media $media
   * @return MediaOrder
   */
  public function setMedia(\Application\Sonata\MediaBundle\Entity\Media $media = null)
  {
    $this->media = $media;

    return $this;
  }

  /**
   * Get media
   *
   * @return \Application\Sonata\MediaBundle\Entity\Media
   */
  public function getMedia()
  {
    return $this->media;
  }
}
