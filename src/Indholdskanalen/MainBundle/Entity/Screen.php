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
   * @Groups({"api"})
   */
  private $channels;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->channels = new ArrayCollection();
	}

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

	/**
	 * Get channelID - used for Middleware serialization
	 *
	 * @return \string
	 *
	 * @VirtualProperty
	 * @SerializedName("channelID")
	 * @Groups({"middleware"})
	 */
	public function getChannelID()
	{
		return $this->getId();
	}

	/**
	 * Get channel groups - used for Middleware serialization
	 *
	 * @return \array
	 *
	 * @VirtualProperty
	 * @SerializedName("groups")
	 * @Groups({"middleware"})
	 */
	public function getChannelGroups()
	{
		return array("group" . $this->getId());
	}

	/**
	 * Get all slides from all channels assigned to this screen - used for Middleware serialization
	 *
	 * @return \array
	 *
	 * @VirtualProperty
	 * @SerializedName("channelContent")
	 * @Groups({"middleware"})
	 */
	public function getChannelContent()
	{
		$slides = array();
		foreach($this->getChannels() as $channel) {
			foreach($channel->getPublishedSlides() as $slide) {
				$slides[] = $slide;
			}
		}
		return array(
			'logo' => '',
			'slides' => $slides
		);
	}
}
