<?php

namespace Indholdskanalen\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ik_grouping")
 */
class Grouping
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Indholdskanalen\MainBundle\Entity\Group
     * @ORM\ManyToOne(targetEntity="Indholdskanalen\MainBundle\Entity\Group", inversedBy="grouping")
     */
    protected $group;

    /**
     * @var string
     * @ORM\Column(name="entity_type", type="string", length=255, nullable=false)
     */
    protected $entityType;

    /**
     * @var string
     * @ORM\Column(name="entity_id", type="integer", nullable=false)
     */
    protected $entityId;

    /**
     * Constructor
     * @param \Indholdskanalen\MainBundle\Entity\Group $group
     * @param \Indholdskanalen\MainBundle\Entity\GroupableEntity $groupable
     */
    public function __construct(Group $group, GroupableEntity $groupable)
    {
        $this->setGroup($group);
        $this->setGroupable($groupable);
    }

    /**
     * Return id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the group object
     *
     * @param Group $group Group to set
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;
    }

    /**
     * Returns the group object
     *
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Sets the groupable
     *
     * @param Groupgable $groupable GroupableEntity to set
     */
    public function setGroupable(GroupableEntity $groupable)
    {
        $this->entityType = $groupable->getGroupableType();
        $this->entityId = $groupable->getGroupableId();
    }

    /**
     * Returns the groupable type
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * Returns the groupable id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

}
