<?php
/**
 * @file
 * Handle entity events that should trigger communication with middleware.
 */

namespace Indholdskanalen\MainBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Indholdskanalen\MainBundle\Services\MiddlewareCommunication;

/**
 * Class MiddlewareListener
 *
 * @package Indholdskanalen\MainBundle\EventListener
 */
class MiddlewareListener {
  protected $middleware;

  /**
   * Default constructor.
   *
   * @param MiddlewareCommunication $middleware
   */
  function __construct(MiddlewareCommunication $middleware) {
    $this->middleware = $middleware;
  }

  /**
   * List to post update events.
   *
   * @param LifecycleEventArgs $args
   */
  public function postUpdate(LifecycleEventArgs $args) {
    // Get the current entity.
    $entity = $args->getEntity();
    $type = get_class($entity);

    // Ignore User, ChannelSlideOrder, MediaOrder. "Order" entities will never be persisted alone, but always as part
	  // of persiting a primary entity.
    if ($type === 'Application\Sonata\UserBundle\Entity\User' ||
      $type === 'Indholdskanalen\MainBundle\Entity\ChannelSlideOrder' ||
      $type === 'Indholdskanalen\MainBundle\Entity\MediaOrder') {
      return;
    }

    $this->middleware->pushChannels();
  }

  /**
   * List to post persist events.
   *
   * @param LifecycleEventArgs $args
   */
  public function postPersist(LifecycleEventArgs $args) {
    // Get the current entity.
    $entity = $args->getEntity();
    $type = get_class($entity);

    if ($type === 'Indholdskanalen\MainBundle\Entity\MediaOrder') {
      $this->middleware->pushChannels();
    }
  }
}
