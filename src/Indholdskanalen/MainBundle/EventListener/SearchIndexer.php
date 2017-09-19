<?php
/**
 * @file
 * Event handlers to send content to the search backend.
 */

namespace Indholdskanalen\MainBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Indholdskanalen\MainBundle\Services\UtilityService;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\Container;
use Indholdskanalen\MainBundle\Entity\GroupableEntity;
use Indholdskanalen\MainBundle\Entity\Group;

/**
 * Class SearchIndexer
 *
 * @package Indholdskanalen\MainBundle\EventListener
 */
class SearchIndexer {
  protected $container;
  protected $serializer;
  protected $utilityService;

  /**
   * Constructor
   *
   * @param Serializer $serializer
   * @param Container $container
   * @param UtilityService $utilityService
   */
  public function __construct(Serializer $serializer, Container $container, UtilityService $utilityService) {
    $this->serializer = $serializer;
    $this->container = $container;
    $this->utilityService = $utilityService;
  }

  /**
   * Listen to post persist events.
   *
   * @param LifecycleEventArgs $args
   */
  public function postPersist(LifecycleEventArgs $args) {
    $this->sendEvent($args, 'POST');
  }

  /**
   * Listen to post-update events.
   *
   * @param LifecycleEventArgs $args
   */
  public function postUpdate(LifecycleEventArgs $args) {
    $this->sendEvent($args, 'PUT');
  }

  /**
   * Listen to pre-remove events.
   *
   * @param LifecycleEventArgs $args
   */
  public function preRemove(LifecycleEventArgs $args) {
    $this->sendEvent($args, 'DELETE');
  }

  /**
   * Helper function to send content/command to the search backend..
   *
   * @param LifecycleEventArgs $args
   *   The arguments send to the original event listener.
   * @param $method
   *   The type of operation to preform.
   *
   * @return boolean
   */
  protected function sendEvent(LifecycleEventArgs $args, $method) {
    // Get the current entity.
    $entity = $args->getEntity();
    $type = get_class($entity);

    // Only send Channel, Screen, Slide, Media to search engine
    if ($type !== 'Indholdskanalen\MainBundle\Entity\Channel' &&
      $type !== 'Indholdskanalen\MainBundle\Entity\Screen' &&
      $type !== 'Indholdskanalen\MainBundle\Entity\Slide' &&
      $type !== 'Application\Sonata\MediaBundle\Entity\Media'
    ) {
      return FALSE;
    }

    // Build parameters to send to the search backend.
    $index = $this->container->getParameter('search_index');
    $params = array(
      'index' => $index,
      'type' => $type,
      'id' => $entity->getId(),
      'data' => $entity,
    );

    // Get search backend URL.
    $url = $this->container->getParameter('search_host');
    $path = $this->container->getParameter('search_path');

    if ($entity instanceof GroupableEntity && $groups = $entity->getGroups()) {
      $entity->setGroups($groups->map(function ($group) {
        return isset($group->id) ? $group->id : $group->getid();
      }));
    }

    $data = $this->serializer->serialize($params, 'json', SerializationContext::create()
        ->setGroups(array('search')));

    // Send the request.
    $result = $this->utilityService->curl($url . $path, $method, $data, 'search');

    if ($result['status'] !== 200) {
      // TODO: Handle !
    }

    return $result['status'] === 200;
  }
}
