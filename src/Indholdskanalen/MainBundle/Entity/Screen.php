<?php
/**
 * @file
 * Screen model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * Extra
 *
 * @ORM\Table(name="ik_screen")
 * @ORM\Entity
 */
class Screen {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api", "api-bulk", "search"})
   */
  private $id;

  /**
   * @ORM\Column(name="title", type="text", nullable=false)
   * @Groups({"api", "api-bulk", "search"})
   */
  private $title;

  /**
   * @ORM\Column(name="orientation", type="string", nullable=false)
   * @Groups({"api", "api-bulk", "search"})
   */
  private $orientation;

  /**
   * @ORM\Column(name="width", type="integer", nullable=false)
   * @Groups({"api", "api-bulk"})
   */
  private $width;

  /**
   * @ORM\Column(name="height", type="integer", nullable=false)
   * @Groups({"api", "api-bulk"})
   */
  private $height;

  /**
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   * @Groups({"api", "api-bulk", "search"})
   */
  private $createdAt;

  /**
   * @ORM\Column(name="token", type="text")
   * @Groups({"api"})
   */
  protected $token;

  /**
   * @ORM\Column(name="activation_code", type="integer")
   * @Groups({"api", "api-bulk"})
   */
  protected $activationCode;

  /**
   * @ORM\OneToMany(targetEntity="ChannelScreenRegion", mappedBy="screen", orphanRemoval=true)
   * @ORM\OrderBy({"sortOrder" = "ASC"})
   * @Groups({"api"})
   */
  private $channelScreenRegions;

  /**
   * @ORM\ManyToMany(targetEntity="SharedChannel", mappedBy="screens")
   * @Groups({"api"})
   */
  private $sharedChannels;

  /**
   * @ORM\Column(name="user", type="integer", nullable=true)
   * @Groups({"api", "search"})
   */
  private $user;

  /**
   * @ORM\Column(name="modified_at", type="integer", nullable=false)
   */
  private $modifiedAt;

  /**
   * @ORM\ManyToOne(targetEntity="ScreenTemplate", inversedBy="screens")
   * @Groups({"api", "api-bulk"})
   */
  private $template;

  /**
   * @ORM\Column(name="description", type="text", nullable=false)
   * @Groups({"api", "api-bulk", "search"})
   */
  private $description;

  /**
   * @ORM\Column(name="options", type="json_array", nullable=true)
   * @Groups({"api", "api-bulk", "search", "sharing", "middleware"})
   */
  private $options;

  /**
   * Constructor
   */
  public function __construct() {
    $this->sharedChannels = new ArrayCollection();
    $this->channelScreenRegions = new ArrayCollection();
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
   * @return Screen
   */
  public function setTitle($title) {
    $this->title = $title;

    return $this;
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
   * Set options
   *
   * @param string $options
   */
  public function setOptions($options) {
    $this->options = $options;
  }

  /**
   * Get options
   *
   * @return string
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * Set description
   *
   * @param string $description
   * @return Screen
   */
  public function setDescription($description) {
    $this->description = $description;

    return $this;
  }

  /**
   * Get description
   *
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Set orientation
   *
   * @param string $orientation
   * @return Screen
   */
  public function setOrientation($orientation) {
    $this->orientation = $orientation;

    return $this;
  }

  /**
   * Get orientation
   *
   * @return string
   */
  public function getOrientation() {
    return $this->orientation;
  }

  /**
   * Set width
   *
   * @param integer $width
   * @return Screen
   */
  public function setWidth($width) {
    $this->width = $width;

    return $this;
  }

  /**
   * Get width
   *
   * @return integer
   */
  public function getWidth() {
    return $this->width;
  }

  /**
   * Set height
   *
   * @param integer $height
   * @return Screen
   */
  public function setHeight($height) {
    $this->height = $height;

    return $this;
  }

  /**
   * Get height
   *
   * @return integer
   */
  public function getHeight() {
    return $this->height;
  }

  /**
   * Set template
   *
   * @param ScreenTemplate $template
   * @return Screen
   */
  public function setTemplate($template) {
    $this->template = $template;

    return $this;
  }

  /**
   * Get template
   *
   * @return ScreenTemplate
   */
  public function getTemplate() {
    return $this->template;
  }

  /**
   * Get token
   *
   * @return mixed
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * Set token
   *
   * @param $token
   */
  public function setToken($token) {
    $this->token = $token;
  }

  /**
   * Get activation code
   *
   * @return mixed
   */
  public function getActivationCode() {
    return $this->activationCode;
  }

  /**
   * Set activation code
   *
   * @param $activationCode
   */
  public function setActivationCode($activationCode) {
    $this->activationCode = $activationCode;
  }

  /**
   * Set createdAt
   *
   * @param integer $createdAt
   * @return Screen
   */
  public function setCreatedAt($createdAt) {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * Get createdAt
   *
   * @return integer
   */
  public function getCreatedAt() {
    return $this->createdAt;
  }

  /**
   * Add sharedChannel
   *
   * @param \Indholdskanalen\MainBundle\Entity\SharedChannel $sharedChannel
   * @return Screen
   */
  public function addSharedChannel(\Indholdskanalen\MainBundle\Entity\SharedChannel $sharedChannel) {
    $sharedChannel->addScreen($this);
    $this->sharedChannels[] = $sharedChannel;

    return $this;
  }

  /**
   * Remove sharedChannel
   *
   * @param \Indholdskanalen\MainBundle\Entity\SharedChannel $sharedChannel
   */
  public function removeSharedChannel(\Indholdskanalen\MainBundle\Entity\SharedChannel $sharedChannel) {
    $sharedChannel->removeScreen($this);
    $this->sharedChannels->removeElement($sharedChannel);
  }

  /**
   * Get sharedChannels
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSharedChannels() {
    return $this->sharedChannels;
  }

  /**
   * Set user
   *
   * @param integer $user
   * @return Screen
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
   * Set modifiedAt
   *
   * @param integer $modifiedAt
   * @return Screen
   */
  public function setModifiedAt($modifiedAt) {
    $this->modifiedAt = $modifiedAt;

    return $this;
  }

  /**
   * Get modifiedAt
   *
   * @return integer
   */
  public function getModifiedAt() {
    return $this->modifiedAt;
  }

  /**
   * Add channelScreenRegion
   *
   * @param \Indholdskanalen\MainBundle\Entity\ChannelScreenRegion $channelScreenRegion
   * @return Screen
   */
  public function addChannelScreenRegion(\Indholdskanalen\MainBundle\Entity\ChannelScreenRegion $channelScreenRegion) {
    $this->channelScreenRegions[] = $channelScreenRegion;

    return $this;
  }

  /**
   * Remove channelScreenRegion
   *
   * @param \Indholdskanalen\MainBundle\Entity\ChannelScreenRegion $channelScreenRegion
   */
  public function removeChannelScreenRegion(\Indholdskanalen\MainBundle\Entity\ChannelScreenRegion $channelScreenRegion) {
    $this->channelScreenRegions->removeElement($channelScreenRegion);
  }

  /**
   * Get channelScreenRegion
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getChannelScreenRegions() {
    return $this->channelScreenRegions;
  }
}
