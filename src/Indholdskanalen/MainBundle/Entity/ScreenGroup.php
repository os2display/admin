<?php
/**
 * @file
 * ScreenGroup model.
 */

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Extra
 *
 * @ORM\Table(name="screen_group")
 * @ORM\Entity
 */
class ScreenGroup {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="title", type="text", nullable=false)
   */
  private $title;

  /**
   * @ORM\ManyToMany(targetEntity="Screen", inversedBy="screen_groups")
   * @ORM\JoinTable(name="screen_group_screens")
   */
  private $screens;

  /**
   * @ORM\Column(name="created", type="integer", nullable=false)
   */
  private $created;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->screens = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ScreenGroup
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
     * Set created
     *
     * @param integer $created
     * @return ScreenGroup
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return integer 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add screens
     *
     * @param \Indholdskanalen\MainBundle\Entity\Screen $screens
     * @return ScreenGroup
     */
    public function addScreen(\Indholdskanalen\MainBundle\Entity\Screen $screens)
    {
        $this->screens[] = $screens;

        return $this;
    }

    /**
     * Remove screens
     *
     * @param \Indholdskanalen\MainBundle\Entity\Screen $screens
     */
    public function removeScreen(\Indholdskanalen\MainBundle\Entity\Screen $screens)
    {
        $this->screens->removeElement($screens);
    }

    /**
     * Get screens
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getScreens()
    {
        return $this->screens;
    }
}
