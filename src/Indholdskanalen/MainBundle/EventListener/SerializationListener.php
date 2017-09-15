<?php
/**
 * @file
 * Contains the SerializationListener.
 */

namespace Indholdskanalen\MainBundle\EventListener;

use Indholdskanalen\MainBundle\Services\TemplateService;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\DependencyInjection\Container;


class SerializationListener implements EventSubscriberInterface {
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
  public function __construct(Pool $mediaService, Container $container, TemplateService $templateService) {
    $this->mediaService = $mediaService;
    $this->container = $container;
    $this->templateService = $templateService;
  }

  /**
   * @inheritdoc
   */
  public static function getSubscribedEvents() {
    return array(
      array(
        'event' => 'serializer.post_serialize',
        'class' => 'Application\Sonata\MediaBundle\Entity\Media',
        'method' => 'onPostMediaSerialize'
      ),
      array(
        'event' => 'serializer.post_serialize',
        'class' => 'Indholdskanalen\MainBundle\Entity\Slide',
        'method' => 'onPostSlideSerialize'
      ),
      array(
        'event' => 'serializer.post_serialize',
        'class' => 'Indholdskanalen\MainBundle\Entity\ScreenTemplate',
        'method' => 'onPostScreenTemplateSerialize'
      ),
    );
  }

  /**
   * Add fields when serializing media.
   *
   * @param ObjectEvent $event
   */
  public function onPostMediaSerialize(ObjectEvent $event) {
    $context = $event->getContext();
    $context->attributes->get('groups')->map(
      function (array $groups) use ($event) {

        // API, Search Serialization
        if (in_array('api', $groups) || in_array('api-bulk', $groups) || in_array('channel', $groups) || in_array('slide', $groups)) {
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
   * Add fields when serializing screen templates.
   *
   * @param ObjectEvent $event
   */
  public function onPostScreenTemplateSerialize(ObjectEvent $event) {
    $context = $event->getContext();
    $context->attributes->get('groups')->map(
      function (array $groups) use ($event) {
        // API, Search Serialization
        if (in_array('api', $groups) || in_array('api-bulk', $groups)) {
          // Get tools as an array and add them to the output.
          $tools = $event->getObject()->getTools();
          $event->getVisitor()->addData('tools', $tools);
        }
      }
    );
  }

  /**
   * Add fields when serializing slide.
   *
   * @param ObjectEvent $event
   */
  public function onPostSlideSerialize(ObjectEvent $event) {
    $context = $event->getContext();
    $context->attributes->get('groups')->map(
      function (array $groups) use ($event) {
        // Middleware Serialization
        if (in_array('middleware', $groups)) {
          $urls = array();

          // Set media paths
          $slide = $event->getObject();
          foreach ($slide->getMedia() as $media) {
            $providerName = $media->getProviderName();

            // Video
            if ($providerName === 'sonata.media.provider.zencoder') {
              $metadata = $media->getProviderMetadata();

              $mediaUrls = array();

              foreach ($metadata as $data) {
                $mediaUrls[$data['label']] = $data['reference'];
              }

              $urls[] = $mediaUrls;
            }

            // Image
            else {
              if ($providerName === 'sonata.media.provider.image') {
                $provider = $this->mediaService->getProvider($providerName);
                $urls[] = array('image' => $provider->generatePublicUrl($media, 'reference'));
              }
            }
          }
          $event->getVisitor()->addData('media', $urls);

          // Set logo path
          $logoPath = '';
          $logo = $slide->getLogo();
          if ($logo) {
            $providerName = $logo->getProviderName();
            if ($providerName === 'sonata.media.provider.image') {
              $provider = $this->mediaService->getProvider($providerName);
              $logoPath = $provider->generatePublicUrl($logo, 'reference');
            }
          }
          $event->getVisitor()->addData('logo', $logoPath);

          // Set template paths
          $template = $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:SlideTemplate')->findOneById($slide->getTemplate());
          $event->getVisitor()->addData('template_path', $template->getPathLive());
          $event->getVisitor()->addData('path', $template->getPath());
          $event->getVisitor()->addData('css_path', $template->getPathCss());
          $event->getVisitor()->addData('js_path', $template->getPathJs());
          $event->getVisitor()->addData('js_script_id', $template->getScriptId());
          $event->getVisitor()->addData('server_path', $this->container->getParameter('absolute_path_to_server'));
        }
        else {
          if (in_array('sharing', $groups)) {
            $urls = array();
            $thumbs = array();

            // Set media paths
            $slide = $event->getObject();
            foreach ($slide->getMedia() as $media) {
              $providerName = $media->getProviderName();

              // Video
              if ($providerName === 'sonata.media.provider.zencoder') {
                $metadata = $media->getProviderMetadata();
                $mediaUrls = array();

                foreach ($metadata as $data) {
                  $mediaUrls[$data['label']] = $data['reference'];
                }

                $thumbs[] = $metadata[0]['thumbnails'][1]['reference'];
                $urls[] = $mediaUrls;
              }
              // Image
              else {
                if ($providerName === 'sonata.media.provider.image') {
                  $provider = $this->mediaService->getProvider($providerName);
                  $urls[] = array('image' => $provider->generatePublicUrl($media, 'reference'));
                  $thumbs[] = $provider->generatePublicUrl($media, 'default_landscape');
                }
              }
            }
            $event->getVisitor()->addData('media', $urls);
            $event->getVisitor()->addData('media_thumbs', $thumbs);

            // Set logo path
            $logoPath = '';
            $logo = $slide->getLogo();
            if ($logo) {
              $providerName = $logo->getProviderName();
              if ($providerName === 'sonata.media.provider.image') {
                $provider = $this->mediaService->getProvider($providerName);
                $logoPath = $provider->generatePublicUrl($logo, 'reference');
              }
            }
            $event->getVisitor()->addData('logo', $logoPath);

            // Set template paths
            $template = $this->container->get('doctrine')->getRepository('IndholdskanalenMainBundle:SlideTemplate')->findOneById($slide->getTemplate());
            $event->getVisitor()
              ->addData('preview_path', $template->getPathPreview());
            $event->getVisitor()
              ->addData('template_path', $template->getPathLive());
            $event->getVisitor()->addData('path', $template->getPath());
            $event->getVisitor()
              ->addData('css_path', $template->getPathCss());
            $event->getVisitor()->addData('js_path', $template->getPathJs());
            $event->getVisitor()->addData('js_script_id', $template->getScriptId());
            $event->getVisitor()->addData('server_path', $this->container->getParameter('absolute_path_to_server'));
          }
        }
      }
    );
  }
}
