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
 * @ORM\Table(name="sharing_index")
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
   * @ORM\Column(name="customer_id", type="text", nullable=false)
   * @Groups({"api"})
   */
  private $customerId;

  /**
   * @ORM\ManyToMany(targetEntity="Channel", inversedBy="sharingIndexes")
   * @ORM\JoinTable(name="sharing_indexes_channels")
   * @Groups({"api"})
   * @MaxDepth(3)
   */
  private $channels;

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
   */
  public function setTitle($name) {
    $this->name = $name;
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
   * Set customerId
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId) {
    $this->customerId = $customerId;
  }

  /**
   * Get customerId
   *
   * @return string
   */
  public function getCustomerId() {
    return $this->customerId;
  }

  /**
   * Add channel
   *
   * @param \Indholdskanalen\MainBundle\Entity\Channel $channel
   * @return Screen
   */
  public function addChannel(\Indholdskanalen\MainBundle\Entity\Channel $channel) {
    $this->channels[] = $channel;

    return $this;
  }

  /**
   * Remove channel
   *
   * @param \Indholdskanalen\MainBundle\Entity\Channel $channel
   */
  public function removeChannel(\Indholdskanalen\MainBundle\Entity\Channel $channel) {
    $this->channels->removeElement($channel);
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
