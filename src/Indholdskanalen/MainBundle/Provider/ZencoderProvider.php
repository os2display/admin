<?php
/**
 * @file
 * Contains the ZencoderProvider.
 */

namespace Indholdskanalen\MainBundle\Provider;

use Sonata\MediaBundle\Provider\BaseProvider;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Gaufrette\Filesystem;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;

/**
 * Class ZencoderProvider
 * @package Indholdskanalen\MainBundle\Provider
 */
class ZencoderProvider extends BaseProvider {
  protected $name;
  protected $hostname;
  protected $apiKey;

  /**
   * Setup the provider with correct hostname and API key.
   *
   * hostname is current hostname taken from paramenters.yml.
   * api_key is Zencoder API KEY.
   *
   * @param string $name
   * @param Filesystem $filesystem
   * @param CDNInterface $cdn
   * @param GeneratorInterface $pathGenerator
   * @param ThumbnailInterface $thumbnail
   * @param $hostname
   * @param $apiKey
   */
  public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, $hostname, $apiKey) {
    parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail);
    $this->name = $name;
    $this->hostname = $hostname;
    $this->apiKey = $apiKey;
  }

  /**
   * Post video to Zencoder.
   *
   * @param MediaInterface $media
   */
  protected function postVideoZencoder(MediaInterface $media) {
    // Generate URL to file.
    $url = $this->getCdn()
        ->getPath($this->generatePath($media), FALSE) . '/' . $media->getProviderReference();

    // Setup formats.
    $mp4 = new \stdClass();
    $mp4->format = 'mp4';
    $mp4->label = 'mp4';
    $mp4->thumbnails = array(
      array(
        'label' => 'mp4_thumbnail',
        'number' => 1,
        'height' => 150,
      ),
      array(
        'label' => 'mp4_landscape',
        'number' => 1,
        'width' => 960,
      ),
    );

    $ogv = new \stdClass();
    $ogv->format = 'ogv';
    $ogv->label = 'ogv';
    $ogv->thumbnails = array(
      array(
        'label' => 'ogv_thumbnail',
        'number' => 1,
        'height' => 150,
      ),
      array(
        'label' => 'ogv_landscape',
        'number' => 1,
        'width' => 960,
      ),
    );

    $webm = new \stdClass();
    $webm->format = 'webm';
    $webm->label = 'webm';
    $webm->thumbnails = array(
      array(
        'label' => 'webm_thumbnail',
        'number' => 1,
        'height' => 150,
      ),
      array(
        'label' => 'webm_landscape',
        'number' => 1,
        'width' => 960,
      ),
    );

    // Setup Zencoder call.
    $api = new \stdClass();
    $api->input = $url;
    $api->api_key = $this->apiKey;
    $api->region = 'europe';
    $api->notifications = $this->hostname . '/zencoder/callback';
    $api->outputs = array(
      $mp4,
      $ogv,
      $webm,
    );

    $json = json_encode($api);

    // Build CURL.
    $ch = curl_init('https://app.zencoder.com/api/v2/jobs');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)
      )
    );

    // Send data to Zencoder.
    $result = json_decode(curl_exec($ch));

    // Save zencoder ID for callback usage.
    if (isset($result->id)) {
      $media->setAuthorName($result->id);
    }
  }

  /**
   * Setup media unique filename.
   *
   * @param MediaInterface $media
   */
  protected function doTransform(MediaInterface $media) {
    // First hit.
    $this->fixBinaryContent($media);
    $this->fixFilename($media);

    // this is the name used to store the file.
    if (!$media->getProviderReference()) {
      $media->setProviderReference($this->generateReferenceName($media));
    }

    if ($media->getBinaryContent()) {
      $media->setContentType($media->getBinaryContent()->getMimeType());
      $media->setSize($media->getBinaryContent()->getSize());
    }

    $media->setProviderStatus(MediaInterface::STATUS_PENDING);

    // Send video to Zencoder.
    $this->postVideoZencoder($media);

  }

  /**
   * Save metadata and file.
   *
   * @param MediaInterface $media
   */
  public function prePersist(MediaInterface $media) {
    // 2. hit.
    if ($media->getBinaryContent() === NULL) {
      return;
    }

    // Retrieve metadata.
    $metadata = $this->getMetadata($media);

    // Store provider information.
    $media->setProviderName($this->name);
    $media->setProviderMetadata($metadata);

    // Update Media common field from metadata.
    $media->setName($metadata['title']);
    $media->setDescription($metadata['description']);
    $media->setAuthorName($metadata['author_name']);
    $media->setHeight($metadata['height']);
    $media->setWidth($metadata['width']);
    $media->setContentType($media->getContentType());
    $media->setProviderStatus(MediaInterface::STATUS_PENDING);

    $media->setCreatedAt(new \Datetime());
    $media->setUpdatedAt(new \Datetime());
  }

  /**
   * Helper function to setup meta information.
   *
   * @param MediaInterface $media
   *
   * @return array
   */
  public function getMetadata(MediaInterface $media) {
    // 3. hit.
    return array(
      'title' => $media->getName(),
      'description' => $media->getDescription(),
      'author_name' => $media->getAuthorName(),
      'height' => $media->getHeight(),
      'width' => $media->getWidth(),
    );
  }

  /**
   * Called when metadata is updated.
   *
   * @param MediaInterface $media
   * @param bool $force
   */
  public function updateMetadata(MediaInterface $media, $force = FALSE) {
  }

  /**
   * @param MediaInterface $media
   * @param string $format
   *
   * @return string
   */
  public function generatePublicUrl(MediaInterface $media, $format) {
    return $this->getCdn()->getPath(sprintf('%s/thumb_%d_%s.jpg',
      $this->generatePath($media),
      $media->getId(),
      $format
    ), $media->getCdnIsFlushable());
  }

  /**
   * Generate path.
   *
   * @param MediaInterface $media
   * @param string $format
   *
   * @return string
   */
  public function generatePrivateUrl(MediaInterface $media, $format) {
    return sprintf('%s/thumb_%d_%s.jpg',
      $this->generatePath($media),
      $media->getId(),
      $format
    );
  }

  /**
   * Setup the media file.
   *
   * @param MediaInterface $media
   */
  public function preUpdate(MediaInterface $media) {
    if (!$media->getBinaryContent()) {
      return;
    }

    $metadata = $this->getMetadata($media);

    $media->setProviderReference($media->getBinaryContent());
    $media->setProviderMetadata($metadata);
    $media->setHeight($metadata['height']);
    $media->setWidth($metadata['width']);
    $media->setProviderStatus(MediaInterface::STATUS_OK);

    $media->setUpdatedAt(new \Datetime());

    $this->fixBinaryContent($media);

    $this->setFileContents($media);
  }

  /**
   * @param MediaInterface $media
   */
  public function postUpdate(MediaInterface $media) {
  }

  /**
   * Save new media.
   *
   * @param MediaInterface $media
   */
  public function postPersist(MediaInterface $media) {
    if ($media->getBinaryContent() === NULL) {
      return;
    }

    $this->setFileContents($media);

  }

  /**
   * Set the file contents for an image.
   *
   * @param \Sonata\MediaBundle\Model\MediaInterface $media
   *
   * @param string|null $contents
   *   Path to contents, defaults to MediaInterface BinaryContent.
   *
   * @return void
   */
  protected function setFileContents(MediaInterface $media, $contents = NULL) {
    $file = $this->getFilesystem()
      ->get(sprintf('%s/%s', $this->generatePath($media), $media->getProviderReference()), TRUE);

    if ($contents === null) {
      $contents = $media->getBinaryContent();
    }

    $metadata = $this->getMetadata($media);
    $file->setContent(file_get_contents($contents), $metadata);
  }

  /**
   * Helper function to make sure content is File object.
   *
   * @param MediaInterface $media
   *
   * @throws \RuntimeException
   */
  protected function fixBinaryContent(MediaInterface $media) {
    if ($media->getBinaryContent() === NULL) {
      return;
    }

    // If the binary content is a filename => convert to a valid File.
    if (!$media->getBinaryContent() instanceof File) {
      if (!is_file($media->getBinaryContent())) {
        throw new \RuntimeException('The file does not exist : ' . $media->getBinaryContent());
      }

      $binary_content = new File($media->getBinaryContent());

      $media->setBinaryContent($binary_content);
    }
  }

  /**
   * Set the file name.
   *
   * @param MediaInterface $media
   *
   * @throws \RuntimeException
   */
  protected function fixFilename(MediaInterface $media) {
    if ($media->getBinaryContent() instanceof UploadedFile) {
      $media->setName($media->getName() ?: $media->getBinaryContent()
          ->getClientOriginalName());
      $media->setMetadataValue('filename', $media->getBinaryContent()
          ->getClientOriginalName());
    }
    elseif ($media->getBinaryContent() instanceof File) {
      $media->setName($media->getName() ?: $media->getBinaryContent()
          ->getBasename());
      $media->setMetadataValue('filename', $media->getBinaryContent()
          ->getBasename());
    }

    // This is the original name.
    if (!$media->getName()) {
      throw new \RuntimeException('Please define a valid media\'s name');
    }
  }

  /**
   * Generate unique filename.
   *
   * @param MediaInterface $media
   *
   * @return string
   */
  protected function generateReferenceName(MediaInterface $media) {
    return sha1($media->getName() . rand(11111, 99999)) . '.' . $media->getBinaryContent()
      ->guessExtension();
  }

  /**
   * Validate function.
   *
   * @param MediaInterface $media
   * @param string $format
   * @param string $mode
   * @param array $headers
   *
   * @return \Symfony\Component\HttpFoundation\Response|void
   */
  public function getDownloadResponse(MediaInterface $media, $format, $mode, array $headers = array()) {
  }

  /**
   * @param MediaInterface $media
   *
   * @return \Gaufrette\File
   */
  public function getReferenceFile(MediaInterface $media) {
    return $this->getFilesystem()->get($this->getReferenceImage($media), TRUE);
  }

  /**
   * @param MediaInterface $media
   *
   * @return string
   */
  public function getReferenceImage(MediaInterface $media) {
    return sprintf('%s/%s',
      $this->generatePath($media),
      $media->getProviderReference()
    );
  }

  /**
   * @param MediaInterface $media
   * @param string $format
   * @param array $options
   *
   * @return array
   */
  public function getHelperProperties(MediaInterface $media, $format, $options = array()) {
    $defaults = array(
      // (optional) Enable full screen capability. Defaults to true.
      'fullscreen' => TRUE,
      // (optional) Show the byline on the video. Defaults to true.
      'title' => TRUE,
      // (optional) Show the user's portrait on the video. Defaults to true.
      'portrait' => TRUE,
    );

    $player_parameters = array_merge($defaults, isset($options['player_parameters']) ? $options['player_parameters'] : array());

    $params = array(
      'src' => http_build_query($player_parameters),
      'width' => isset($options['width']) ? $options['width'] : $media->getWidth(),
      'height' => isset($options['height']) ? $options['height'] : $media->getHeight(),
    );

    return $params;
  }

  /**
   * @param FormBuilder $formBuilder
   */
  public function buildMediaType(FormBuilder $formBuilder) {
    $formBuilder->add('binaryContent', 'text');
  }

  /**
   * @param FormMapper $formMapper
   */
  public function buildCreateForm(FormMapper $formMapper) {
    $formMapper->add('binaryContent', array(), array('type' => 'string'));
  }

  /**
   * @param FormMapper $formMapper
   */
  public function buildEditForm(FormMapper $formMapper) {
    $formMapper->add('name');
    $formMapper->add('enabled');
    $formMapper->add('authorName');
    $formMapper->add('cdnIsFlushable');
    $formMapper->add('description');
    $formMapper->add('copyright');
    $formMapper->add('binaryContent', array(), array('type' => 'string'));
  }
}
