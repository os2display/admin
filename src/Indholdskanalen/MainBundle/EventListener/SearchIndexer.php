<?php
/**
 * @file
 * Event handlers to send content to the search backend.
 */

namespace Indholdskanalen\MainBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Session\Session;


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
   * @param Container $container
   */
  function __construct(Serializer $serializer, Container $container) {
    $this->serializer = $serializer;
    $this->container = $container;
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
   * Authenticate with the sharing service.
   *
   * @return bool
   */
  private function curlAuthenticate() {
    $apikey = $this->container->getParameter('search_apikey');
    $search_host = $this->container->getParameter('search_host');

    $data = json_encode(
      array(
        'apikey' => $apikey
      )
    );

    // Build, execute and close query.
    $ch = curl_init($search_host . "/authenticate");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_status === 200) {
      return json_decode($content)->token;
    }
    else {
      return false;
    }
  }

  /**
   * Get the authentication token from session else from Service.
   *
   * @return bool|mixed|null
   */
  public function searchAuthenticate() {
    $session = new Session();
    $token = null;

    // If the token is set return it.
    if ($session->has('search_token')) {
      $token = $session->get('search_token');
    }
    else {
      $token = $this->curlAuthenticate();
      if ($token) {
        $session->set('search_token', $token);
      }
    }

    return $token;
  }

  /**
   * Remove session token and authenticate from Service.
   *
   * @return bool|mixed|null
   */
  public function searchReauthenticate() {
    $session = new Session();
    $session->remove('search_token');

    $token = $this->curlAuthenticate();
    if ($token) {
      $session->set('search_token', $token);
    }

    return $token;
  }

  /**
   * Helper function to send content/command to the search backend..
   *
   * @param LifecycleEventArgs $args
   *   The arguments send to the original event listener.
   * @param $method
   *   The type of operation to preform.
   *
   * @return bool
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

    // Send the request.
    $data = $this->curl($url . $path, $method, $params);

    // @TODO: Do some error handling based on the $data['status'] variable.
  }

  private function buildQuery($url, $method, $data, $token) {
    // Build query.
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $token
    ));
    // Receive server response.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    return $ch;
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
   * @return array
   */
  protected function curl($url, $method = 'POST', $params = array()) {
    // Get the authentication token.
    $token = $this->searchAuthenticate();

    $jsonContent = $this->serializer->serialize($params, 'json', SerializationContext::create()->setGroups(array('search')));

    $ch = $this->buildQuery($url, $method, $jsonContent, $token);
    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // Close connection.
    curl_close($ch);

    // If unauthenticated, reauthenticate and retry.
    if ($http_status === 401) {
      $token = $this->searchReauthenticate();

      $ch = $this->buildQuery($url, $method, $jsonContent, $token);
      $content = curl_exec($ch);
      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      // Close connection.
      curl_close($ch);
    }

    return array(
      'status' => $http_status,
      'content' => $content,
    );
  }
}
