<?php

namespace Os2Display\CoreBundle\Entity;

interface GroupableEntity {

  public function getGroupableType();

  public function getGroupableId();

  public function getGroups();
}
