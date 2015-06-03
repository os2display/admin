<?php
/**
 * @file
 * SharingIndex model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;


/**
 * SharingIndex
 * Represents an index existing in the sharing service.
 *
 * @ORM\Table(name="ik_sharing_index")
 * @ORM\Entity
 */
class SharingIndex {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api"})
   */
  private $id;

  /**
   * @ORM\Column(name="name", type="text", nullable=false)
   * @Groups({"api"})
   */
  private $name;

  /**
   * @ORM\Column(name="`index`", type="text", nullable=false)
   * @Groups({"api"})
   */
  private $index;

  /**
   * @ORM\ManyToMany(targetEntity="Channel", inversedBy="sharingIndexes")
   * @ORM\JoinTable(name="ik_sharing_indexes_channels")
   * @Groups({"api"})
   * @MaxDepth(3)
   */
  private $channels;

  /**
   * @ORM\Column(name="enabled", type="boolean", nullable=true)
   * @Groups({"api"})
   */
  private $enabled;

  /**
   * Constructor
   */
  public function __construct() {
    $this->channels = new ArrayCollection();
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
   * Set name
   *
   * @param string $name
   * @return SharingIndex
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }


  /**
   * Set enabled
   *
   * @param boolean $enabled
   * @return SharingIndex
   */
  public function setEnabled($enabled) {
    $this->enabled = $enabled;

    return $this;
  }

  /**
   * Get enabled
   *
   * @return boolean
   */
  public function getEnabled() {
    return $this->enabled;
  }

  /**
   * Set index
   *
   * @param string $index
   * @return SharingIndex
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
   * Add channel
   *
   * @param \Indholdskanalen\MainBundle\Entity\Channel $channel
   * @return SharingIndex
   */
  public function addChannel(\Indholdskanalen\MainBundle\Entity\Channel $channel) {
    $this->channels[] = $channel;

    return $this;
  }

  /**
   * Remove channel
   *
   * @param \Indholdskanalen\MainBundle\Entity\Channel $channel
   * @return SharingIndex
   */
  public function removeChannel(\Indholdskanalen\MainBundle\Entity\Channel $channel) {
    $this->channels->removeElement($channel);

    return $this;
  }

  /**
   * Get channels
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getChannels() {
    return $this->channels;
  }
}
