<?php

namespace Indholdskanalen\MainBundle\Entity;

interface GroupableEntity {

  public function getGroupableType();

  public function getGroupableId();

  public function getGroups();
}
