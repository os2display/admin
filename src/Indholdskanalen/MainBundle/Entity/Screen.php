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
	private $created_at;

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
	 * @ORM\ManyToMany(targetEntity="Channel", mappedBy="screens")
	 * @Groups({"api"})
	 * @MaxDepth(6)
	 */
	private $channels;

  /**
   * @ORM\ManyToMany(targetEntity="SharedChannel", mappedBy="screens")
   * @Groups({"api"})
   */
  private $shared_channels;

	/**
	 * @ORM\Column(name="user", type="integer", nullable=true)
	 * @Groups({"api", "search"})
	 */
	private $user;

  /**
   * @ORM\Column(name="modified_at", type="integer", nullable=false)
   */
  private $modified_at;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->channels = new ArrayCollection();
    $this->shared_channels = new ArrayCollection();
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
   * Add shared_channel
   *
   * @param \Indholdskanalen\MainBundle\Entity\SharedChannel $sharedChannel
   * @return Screen
   */
  public function addSharedChannel(\Indholdskanalen\MainBundle\Entity\SharedChannel $sharedChannel) {
    $sharedChannel->addScreen($this);
    $this->shared_channels[] = $sharedChannel;

    return $this;
  }

  /**
   * Remove shared_channel
   *
   * @param \Indholdskanalen\MainBundle\Entity\SharedChannel $sharedChannel
   */
  public function removeSharedChannel(\Indholdskanalen\MainBundle\Entity\SharedChannel $sharedChannel) {
    $sharedChannel->removeScreen($this);
    $this->shared_channels->removeElement($sharedChannel);
  }

  /**
   * Get shared_channels
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSharedChannels() {
    return $this->shared_channels;
  }

	/**
	 * Set user
	 *
	 * @param integer $user
	 * @return Screen
	 */
	public function setUser($user)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get user
	 *
	 * @return integer
	 */
	public function getUser()
	{
		return $this->user;
	}


  /**
   * Set modified_at
   *
   * @param integer $modifiedAt
   * @return Screen
   */
  public function setModifiedAt($modifiedAt) {
    $this->modified_at = $modifiedAt;

    return $this;
  }

  /**
   * Get modified_at
   *
   * @return integer
   */
  public function getModifiedAt() {
    return $this->modified_at;
  }

}
