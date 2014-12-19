<?php
/**
 * @file
 *
 *
 * @TODO: Move curl into utils.
 */

namespace Indholdskanalen\MainBundle\Command;

use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Indholdskanalen\MainBundle\Entity\Slide;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;
use Sonata\MediaBundle\Provider;

/**
 * Class PushScheduleCommand
 *
 * @package Indholdskanalen\MainBundle\Command
 */
class SearchCommand extends ContainerAwareCommand {
  private $output;
  private $serializer;

  /**
   * Configure the command
   */
  protected function configure() {
    $this->setName('indholdskanalen:reindex')
      ->setDescription("Re-index all in the search backend.");
  }

  /**
   * Executes the command
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   *
   * @return int|null|void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->serializer = $this->getContainer()->get('jms_serializer');
    $this->output = $output;

    // Find all media elements.
    $entities = $this->getContainer()->get('doctrine')->getRepository('ApplicationSonataMediaBundle:Media')->findAll();

    // Loop over the elements to add real urls.
    foreach ($entities as &$media) {
      // Get provider and the supported formats.
      $provider = $this->getContainer()->get($media->getProviderName());
      $formats = $provider->getFormats();

      // Get the urls.
      $urls = array();
      foreach ($formats as $name => $format) {
        $urls[$name] = $provider->generatePublicUrl($media, $name);
      }

      // Store them for later (see curl serializer hack).
      $media->urls = $urls;
    }
    $this->indexEnities('Media elements', $entities);

    // Find all slides.
    $entities = $this->getContainer()->get('doctrine')->getRepository('IndholdskanalenMainBundle:Slide')->findAll();
    $this->indexEnities('Slides', $entities);

    // Find all Channels.
    $entities = $this->getContainer()->get('doctrine')->getRepository('IndholdskanalenMainBundle:Channel')->findAll();
    $this->indexEnities('Channels', $entities);

    // Find all slides.
    $entities = $this->getContainer()->get('doctrine')->getRepository('IndholdskanalenMainBundle:Screen')->findAll();
    $this->indexEnities('Screens', $entities);
  }

  /**
   * Index the entities given.
   *
   * @param string $type
   *   The type of entities to index (only used in the print).
   * @param array $entities
   *   The entities to add to the search backend.
   */
  private function indexEnities($type, $entities) {
    $this->output->write(sprintf('Found %d %s ', count($entities), $type));

    foreach ($entities as $entity) {
      $this->indexEntity($entity);
    }

    // Make a newline.
    $this->output->writeln('');
  }

  /**
   * Add a single entity to the search backend.
   *
   * @param Entity $entity
   *   The entity to add to the search backend.
   * @param string $cmd
   *   The command to use "POST" create, "PUT" update at the search backend.
   */
  private function indexEntity($entity, $cmd = 'POST') {
    $data = $this->sendEvent($entity, $cmd);
    if ($data->status == 200) {
      $this->output->write(sprintf('.'));
    }
    elseif ($data->status == 409) {
      // Document already exists, so update.
      $this->indexEntity($entity, 'PUT');
    }
    else {
      print_r($data);
      $this->output->write(sprintf('F'));
    }
  }

  /**
   * Helper function to send content/command to the search backend..
   *
   * @param Entity $entity
   *   Entity to send to the search backend.
   * @param $method
   *   The type of operation to preform.
   *
   * @return bool
   */
  private function sendEvent($entity, $method) {
    // Build parameters to send to the search backend.
    $index = $this->getContainer()->getParameter('search_index');
    $params = array(
      'index' => $index,
      'type' => get_class($entity),
      'id' => $entity->getId(),
      'data' => $entity,
    );

    // Get search backend URL.
    $url = $this->getContainer()->getParameter('search_host');
    $path = $this->getContainer()->getParameter('search_path');

    // Send the request.
    return $this->curl($url . $path, $method, $params);
  }

  /**
   * Builds the curl query.
   *
   * @param $url
   * @param $method
   * @param $data
   * @param $token
   * @return resource
   */
  private function buildQuery($url, $method, $data, $token) {
    // Build query.
    $ch = curl_init($url);
    
    // SSL fix (self signed).
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

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
  private function curl($url, $method = 'POST', $params) {
    $authenticationService = $this->getContainer()->get('indholdskanalen.authentication_service');

    // Get the authentication token.
    $auth = $authenticationService->getAuthentication('search');

    $data = $this->serializer->serialize($params, 'json', SerializationContext::create()->setGroups(array('search')));

    // Build query.
    $ch = $this->buildQuery($url, $method, $data, $auth['token']);
    $content = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close connection.
    curl_close($ch);

    // If unauthenticated, reauthenticate and retry.
    if ($http_status === 401) {
      $auth = $authenticationService->getAuthentication('search', true);

      $ch = $this->buildQuery($url, $method, $data, $auth['token']);
      $content = curl_exec($ch);
      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      // Close connection.
      curl_close($ch);
    }

    return (object) array(
      'status' => $http_status,
      'content' => $content,
    );
  }
}
