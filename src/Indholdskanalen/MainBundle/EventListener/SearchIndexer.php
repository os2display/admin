<?php
/**
 * @file
 * Event handlers to send content to the search backend.
 */

namespace Indholdskanalen\MainBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use JMS\Serializer\Serializer;

/**
 * Class SearchIndexer
 *
 * @package Indholdskanalen\MainBundle\EventListener
 */
class SearchIndexer {
  protected $container;
  protected $serializer;

  /**
   * Default constructor.
   *
   * @param Serializer $serializer
   */
  function __construct(Serializer $serializer) {
    $this->serializer = $serializer;
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
   * @param array $args
   *   The arguments send to the original event listener.
   * @param $method
   *   The type of operation to preform.
   *
   * @return bool
   */
  protected function sendEvent($args, $method) {
    // Get the current entity.
    $entity = $args->getEntity();
    $type = get_class($entity);

    // We will not send user data to ES.
    if ($type == 'Application\Sonata\UserBundle\Entity\User') {
      return FALSE;
    }

    $this->curl('http://localhost:3001/api', $method, array('app_id' => '1234', 'app_secret' => 'test', 'type' => $type, 'data' => $entity));

  }

  /**
   * Communication function.
   *
   * Wrapper function for curl to send data to ES.
   *
   * @param $url
   *   URL to connect to.
   * @param string $method
   *   Method to send/get data "POST" or "PUT".
   * @param array $params
   *   The data to send.
   *
   * @return \stdClass|string
   */
  protected function curl($url, $method = 'POST', $params = array()) {
    // Build query.
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    $jsonContent = $this->serializer->serialize($params, 'json');

    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonContent);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));

    // Receive server response.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close connection.
    curl_close($ch);
  }
}
