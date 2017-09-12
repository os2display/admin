<?php
/**
 * @file
 * ChannelSlideOrder model.
 */

namespace Os2Display\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Application\Sonata\MediaBundle\Entity\Media as Media;


/**
 * Media Order.
 * Represents an ordered media element.
 *
 * @ORM\Table(name="ik_media_order")
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
   * @ORM\ManyToOne(targetEntity="Slide", inversedBy="mediaOrders")
   * @ORM\JoinColumn(name="slide_id", referencedColumnName="id")
   */
  private $slide;

  /**
   * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", inversedBy="mediaOrders")
   * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
   */
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
   * @return MediaOrder
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
   * Set slide
   *
   * @param null|\Os2Display\CoreBundle\Entity\Slide $slide
   * @return MediaOrder
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
   * Set media
   *
   * @param null|\Application\Sonata\MediaBundle\Entity\Media $media
   * @return MediaOrder
   */
  public function setMedia(\Application\Sonata\MediaBundle\Entity\Media $media = NULL) {
    $this->media = $media;

    return $this;
  }

  /**
   * Get media
   *
   * @return null|\Application\Sonata\MediaBundle\Entity\Media
   */
  public function getMedia() {
    return $this->media;
  }
}
