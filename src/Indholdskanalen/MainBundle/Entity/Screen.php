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
   * @MaxDepth(8)
   */
  private $channelScreenRegions;

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
   * @return Screen
   */
  public function setOptions($options) {
    $this->options = $options;

    return $this;
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
   * @return Screen
   */
  public function setToken($token) {
    $this->token = $token;

    return $this;
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
   * @return Screen
   */
  public function setActivationCode($activationCode) {
    $this->activationCode = $activationCode;

    return $this;
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
   * @return Screen
   */
  public function removeChannelScreenRegion(\Indholdskanalen\MainBundle\Entity\ChannelScreenRegion $channelScreenRegion) {
    $this->channelScreenRegions->removeElement($channelScreenRegion);

    return $this;
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
