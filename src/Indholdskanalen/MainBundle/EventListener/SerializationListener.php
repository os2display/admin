<?php

namespace Indholdskanalen\MainBundle\EventListener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Sonata\MediaBundle\Provider\Pool;
use Indholdskanalen\MainBundle\Entity\Slide;


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
      array('event' => 'serializer.post_serialize', 'class' => 'Application\Sonata\MediaBundle\Entity\Media', 'method' => 'onPostMediaSerialize'),
      array('event' => 'serializer.post_serialize', 'class' => 'Indholdskanalen\MainBundle\Entity\Slide', 'method' => 'onPostSlideSerialize'),
    );
  }

  public function onPostMediaSerialize(ObjectEvent $event)
  {
    $context = $event->getContext();
    $context->attributes->get('groups')->map(
      function(array $groups) use ($event) {

        // API Serialization
        if (in_array('api', $groups)) {
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
    );

  }

  public function onPostSlideSerialize(ObjectEvent $event)
  {
    $context = $event->getContext();
    $context->attributes->get('groups')->map(
      function(array $groups) use ($event) {

        // Middleware Serialization
        if (in_array('middleware', $groups)) {
          $slide = $event->getObject();
          $urls = array();

          if ($slide->getMediaType() === 'image') {
            foreach($slide->getMedia() as $media) {
              $provider = $this->mediaService->getProvider($media->getProviderName());
              $urls[] = $provider->generatePublicUrl($media, 'reference');
            }
          } else if ($slide->getMediaType() === 'video') {
            $urls = array(
              'mp4' => $pathToServer . $content->provider_metadata[0]->reference,
              'ogg' => $pathToServer . $content->provider_metadata[1]->reference,
            );
          }


          $event->getVisitor()->addData('media', $urls);
        }
      }
    );
  }

}
