<?php
/**
 * @file
 * Screen model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Extra
 *
 * @ORM\Table(name="screen")
 * @ORM\Entity
 */
class Screen {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Groups({"api", "search"})
   */
  private $id;

  /**
   * @ORM\Column(name="title", type="text", nullable=false)
   * @Groups({"api", "search"})
   */
  private $title;

  /**
   * @ORM\Column(name="orientation", type="string", nullable=false)
   * @Groups({"api", "search"})
   */
  private $orientation;

  /**
   * @ORM\Column(name="width", type="integer", nullable=false)
   * @Groups({"api"})
   */
  private $width;

  /**
   * @ORM\Column(name="height", type="integer", nullable=false)
   * @Groups({"api"})
   */
  private $height;

  /**
   * @ORM\ManyToMany(targetEntity="ScreenGroup", mappedBy="screens")
   * @Groups({"api"})
   */
  private $groups;

  /**
   * @ORM\Column(name="created_at", type="integer", nullable=false)
   * @Groups({"api", "search"})
   */
  private $created_at;

  /**
   * @ORM\Column(name="token", type="text")
   * @Groups({"api"})
   */
  protected $token;

  /**
   * @ORM\Column(name="activation_code", type="integer")
   * @Groups({"api"})
   */
  protected $activationCode;

  /**
   * @ORM\ManyToMany(targetEntity="Channel", mappedBy="screens")
   */
  private $channels;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set title
   *
   * @param string $title
   * @return Screen
   */
  public function setTitle($title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Set orientation
   *
   * @param string $orientation
   * @return Screen
   */
  public function setOrientation($orientation)
  {
    $this->orientation = $orientation;

    return $this;
  }

  /**
   * Get orientation
   *
   * @return string
   */
  public function getOrientation()
  {
    return $this->orientation;
  }

  /**
   * Set width
   *
   * @param integer $width
   * @return Screen
   */
  public function setWidth($width)
  {
    $this->width = $width;

    return $this;
  }

  /**
   * Get width
   *
   * @return integer
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * Set height
   *
   * @param integer $height
   * @return Screen
   */
  public function setHeight($height)
  {
    $this->height = $height;

    return $this;
  }

  /**
   * Get height
   *
   * @return integer
   */
  public function getHeight()
  {
    return $this->height;
  }

  /**
   * Get token
   *
   * @return mixed
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * Set token
   *
   * @param $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }

  /**
   * Get activation code
   *
   * @return mixed
   */
  public function getActivationCode()
  {
    return $this->activationCode;
  }

  /**
   * Set activation code
   *
   * @param $activationCode
   */
  public function setActivationCode($activationCode)
  {
    $this->activationCode = $activationCode;
  }

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    $this->channels = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Add groups
   *
   * @param \Indholdskanalen\MainBundle\Entity\ScreenGroup $groups
   * @return Screen
   */
  public function addGroup(\Indholdskanalen\MainBundle\Entity\ScreenGroup $groups)
  {
    $groups->addScreen($this);
    $this->groups[] = $groups;

    return $this;
  }

  /**
   * Remove groups
   *
   * @param \Indholdskanalen\MainBundle\Entity\ScreenGroup $groups
   */
  public function removeGroup(\Indholdskanalen\MainBundle\Entity\ScreenGroup $groups)
  {
    $groups->removeScreen($this);
    $this->groups->removeElement($groups);
  }

  /**
   * Get groups
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getGroups()
  {
    return $this->groups;
  }

  /**
   * Set created_at
   *
   * @param integer $createdAt
   * @return Screen
   */
  public function setCreatedAt($createdAt)
  {
    $this->created_at = $createdAt;

    return $this;
  }

  /**
   * Get created_at
   *
   * @return integer
   */
  public function getCreatedAt()
  {
    return $this->created_at;
  }

  /**
   * Add channel
   *
   * @param \Indholdskanalen\MainBundle\Entity\Channel $channel
   * @return Screen
   */
  public function addChannel(\Indholdskanalen\MainBundle\Entity\Channel $channel)
  {
    $channel->addScreen($this);
    $this->channels[] = $channel;

    return $this;
  }

  /**
   * Remove channel
   *
   * @param \Indholdskanalen\MainBundle\Entity\Channel $channel
   */
  public function removeChannel(\Indholdskanalen\MainBundle\Entity\Channel $channel)
  {
    $channel->removeScreen($this);
    $this->channels->removeElement($channel);
  }

  /**
   * Get channels
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getChannels()
  {
    return $this->channels;
  }
}
