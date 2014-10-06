<?php
/**
 * @file
 * ChannelSlideOrder model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
   */
  private $id;

  /**
   * @ORM\Column(name="sort_order", type="integer")
   */
  private $sortOrder;

  /**
   * @ORM\ManyToOne(targetEntity="Slide", inversedBy="slide_media_orders")
   * @ORM\JoinColumn(name="slide_id", referencedColumnName="id")
   **/
  private $slide;

  /**
   * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", inversedBy="slide_media_orders")
   * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
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
   * @param \Indholdskanalen\MainBundle\Entity\Media $media
   * @return MediaOrder
   */
  public function setMedia(\Indholdskanalen\MainBundle\Entity\Media $media = null)
  {
    $this->media = $media;

    return $this;
  }

  /**
   * Get media
   *
   * @return \Indholdskanalen\MainBundle\Entity\Media
   */
  public function getMedia()
  {
    return $this->media;
  }
}
