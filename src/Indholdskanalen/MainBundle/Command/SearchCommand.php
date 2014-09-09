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
use Sonata\MediaBundle\Provider;

/**
 * Class PushScheduleCommand
 *
 * @package Indholdskanalen\MainBundle\Command
 */
class SearchCommand extends ContainerAwareCommand {
  private $output;

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

  private function indexEnities($type, $entities) {
    $this->output->write(sprintf('Found %d %s ', count($entities), $type));

    foreach ($entities as $entity) {
      $this->indexEntity($entity);
    }

    // Make a newline.
    $this->output->writeln('');
  }

  private function indexEntity($entity, $cmd = 'PUT') {
    $data = $this->sendEvent($entity, $cmd);
    if ($data->status == 200) {
      $this->output->write(sprintf('.'));
    }
    elseif ($data->status == 409) {
      // Document already exists, so update.
      $this->indexEntity($entity, 'POST');
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
    $customer_id = $this->getContainer()->getParameter('search_customer_id');
    $params = array(
      'customer_id' => $customer_id,
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
    // Build query.
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    // Get serializer to encode the entity.
    $serializer = SerializerBuilder::create()->build();
    $jsonContent = $serializer->serialize($params, 'json');

    // @TODO: This is a HACK to get urls into the data sent to search.
    if (isset($params['data']->urls)) {
      $data = json_decode($jsonContent);
      $data->data->urls = $params['data']->urls;
      $jsonContent = json_encode($data);
    }

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

    return (object) array(
      'status' => $http_status,
      'content' => $content,
    );
  }
}
