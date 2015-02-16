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
   * Listen to pre-update events.
   *
   * @param LifecycleEventArgs $args
   */
  public function preUpdate(LifecycleEventArgs $args) {
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

    // We will not send user data to ES.
    // Ignore ChannelSlideOrders and MediaOrders as well.
    if ($type === 'Application\Sonata\UserBundle\Entity\User' ||
        $type === 'Indholdskanalen\MainBundle\Entity\ChannelSlideOrder' ||
        $type === 'Indholdskanalen\MainBundle\Entity\MediaOrder' ||
        $type === 'Indholdskanalen\MainBundle\Entity\SharingIndex') {
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

    $data = $this->serializer->serialize($params, 'json', SerializationContext::create()->setGroups(array('search')));

    // Send the request.
    $result = $this->utilityService->curl($url . $path, $method, $data, 'search');

    if ($result['status'] !== 200) {
      // TODO: Handle !
    }

    return $result['status'] === 200;
  }
}
