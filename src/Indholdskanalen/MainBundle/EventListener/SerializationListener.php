<?php

namespace Indholdskanalen\MainBundle\EventListener;

use Indholdskanalen\MainBundle\Services\TemplateService;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\DependencyInjection\Container;


class SerializationListener implements EventSubscriberInterface
{
  /**
   * @var \Sonata\MediaBundle\Provider\Pool
   */
  protected $mediaService;

	/**
	 * @var \Symfony\Component\DependencyInjection\Container
	 */
	protected $container;

  /**
   * @var TemplateService
   */
  protected $templateService;

  /**
   * @param Pool $mediaService
   * @param Container $container
   * @param TemplateService $templateService
   */
  public function __construct(Pool $mediaService, Container $container, TemplateService $templateService)
  {
    $this->mediaService = $mediaService;
	  $this->container = $container;
    $this->templateService = $templateService;
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

  /**
   * Add fields when serializing media.
   *
   * @param ObjectEvent $event
   */
  public function onPostMediaSerialize(ObjectEvent $event)
  {
    $context = $event->getContext();
    $context->attributes->get('groups')->map(
      function(array $groups) use ($event) {

        // API, Search Serialization
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

  /**
   * Add fields when serializing slide.
   *
   * @param ObjectEvent $event
   */
	public function onPostSlideSerialize(ObjectEvent $event)
	{
		$context = $event->getContext();
		$context->attributes->get('groups')->map(
			function(array $groups) use ($event) {
				// Middleware Serialization
				if (in_array('middleware', $groups)) {
          $urls = array();

          $slide = $event->getObject();
					foreach($slide->getMedia() as $media) {
						$providerName = $media->getProviderName();

						// Video
						if($providerName === 'sonata.media.provider.zencoder') {
							$pathToServer = $this->container->getParameter("absolute_path_to_server");
							$metadata = $media->getProviderMetadata();
							foreach($metadata as $data) {
								$urls[$data['label']] = $pathToServer.$data['reference'];
							}
						}

						// Image
						else if($providerName === 'sonata.media.provider.image') {
							$provider = $this->mediaService->getProvider($providerName);
							$urls[] = $provider->generatePublicUrl($media, 'reference');
						}
					}
          $event->getVisitor()->addData('media', $urls);

          $templates = $this->templateService->getTemplates();
          $event->getVisitor()->addData('template_path', $templates[$slide->getTemplate()]->paths->live);
          $event->getVisitor()->addData('css_path', $templates[$slide->getTemplate()]->paths->css);
				}
			}
		);
	}
}
