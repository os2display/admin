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

    // Do not push channels on user update.
    if ($type === 'Application\Sonata\UserBundle\Entity\User') {
      return;
    }

    $this->middleware->pushChannels();
  }
}
