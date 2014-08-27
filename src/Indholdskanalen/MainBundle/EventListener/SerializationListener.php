<?php

namespace Indholdskanalen\MainBundle\EventListener;


use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Sonata\MediaBundle\Provider\Pool;
use Sonata\MediaBundle\Model\Media;


class SerializationListener implements EventSubscriberInterface
{
  /**
   * @var \Sonata\MediaBundle\Provider\Pool
   */
  protected $mediaService;

  /**
   * @param Pool $mediaService
   */
  public function __construct(Pool $mediaService)
  {
    $this->mediaService = $mediaService;
  }

  /**
   * @inheritdoc
   */
  static public function getSubscribedEvents()
  {
    return array(
      array('event' => 'serializer.post_serialize', 'class' => 'Application\Sonata\MediaBundle\Entity\Media', 'method' => 'onPostSerialize'),
    );
  }

  public function onPostSerialize(ObjectEvent $event)
  {
    $media = $event->getObject();
    $provider = $this->mediaService->getProvider($media->getProviderName());
    $formats = $provider->getFormats();

    $urls = array();
    foreach ($formats as $name => $format) {
      $urls[$name] = $provider->generatePublicUrl($media, $name);
    }

    $event->getVisitor()->addData('urls', $urls);
  }

}

?>