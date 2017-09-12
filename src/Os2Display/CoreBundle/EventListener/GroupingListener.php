<?php

namespace Os2Display\CoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Os2Display\CoreBundle\Entity\GroupableEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class is actually a workaround for not doing this in GroupManager (cyclic service dependencies).
 *
 * Class GroupingListener
 * @package Os2Display\CoreBundle\EventListener
 */
class GroupingListener implements EventSubscriber {
  /**
   * @var \Os2Display\CoreBundle\Services\GroupManager
   */
  protected $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  public function getSubscribedEvents() {
    return [Events::postPersist, Events::postUpdate, Events::postLoad, Events::preRemove];
  }

  public function postPersist(LifecycleEventArgs $args) {
    $entity = $args->getObject();
    if ($entity instanceof GroupableEntity) {
      $groupManager = $this->container->get('os2display.group_manager');
      $groupManager->replaceGroups($entity->getGroups(), $entity);
      $groupManager->saveGrouping($entity);
    }
  }

  public function postUpdate(LifecycleEventArgs $args) {
    $this->postPersist($args);
  }

  public function postLoad(LifecycleEventArgs $args) {
    $entity = $args->getObject();
    if ($entity instanceof GroupableEntity) {
      $groupManager = $this->container->get('os2display.group_manager');
      $groupManager->loadGrouping($entity);
    }
  }

  public function preRemove(LifecycleEventArgs $args) {
    $entity = $args->getObject();
    if ($entity instanceof GroupableEntity) {
      $groupManager = $this->container->get('os2display.group_manager');
      $groupManager->deleteGrouping($entity);
    }
  }
}
