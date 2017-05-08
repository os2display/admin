<?php

namespace Indholdskanalen\MainBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Indholdskanalen\MainBundle\Entity\GroupableEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class is actually a workaround for not doing this in GroupManager (cyclic service dependencies).
 *
 * Class GroupingListener
 * @package Indholdskanalen\MainBundle\EventListener
 */
class GroupingListener implements EventSubscriber {
  /**
   * @var \Indholdskanalen\MainBundle\Services\GroupManager
   */
  protected $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  public function getSubscribedEvents() {
    return [Events::preRemove];
  }

  public function preRemove(LifecycleEventArgs $args) {
    $entity = $args->getObject();
    if ($entity instanceof GroupableEntity) {
      $groupManager = $this->container->get('os2display.group_manager');
      $groupManager->deleteGrouping($entity);
    }
  }
}
