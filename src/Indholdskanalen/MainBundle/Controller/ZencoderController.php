<?php

namespace Indholdskanalen\MainBundle\Controller;

use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/zencoder")
 */
class ZencoderController extends Controller {
  /**
   * Handle callback from Zencoder.
   *
   * @Route("/callback")
   * @Method("POST")
   *
   * @param $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function CallbackAction(Request $request) {
    // Get posted channel information from the request.
    $post = json_decode($request->getContent());

    $status = FALSE;

    // Find the correct media.
    $local_media = $this->getDoctrine()->getRepository('ApplicationSonataMediaBundle:Media')
      ->findOneByAuthorName($post->job->id);

    if ($local_media) {
      $cdn = $this->get('sonata.media.cdn.server');
      $zencoder = $this->get('sonata.media.provider.zencoder');
      $root = $this->get('kernel')->getRootDir() . '/../web';
      $path = $root . $cdn->getPath($zencoder->generatePath($local_media), FALSE);

      $transcoded = array();

      // More outputs from Zencoder. No problem.
      foreach ($post->outputs as $output) {
        // Save the transcoded video.
        $video_filename = basename(substr($output->url, 0, strpos($output->url, '?')));
        file_put_contents($path . '/' . $video_filename, file_get_contents($output->url));

        // Thumbnails. We save the first thumbnail per output.
        $thumbnails = array();
        foreach ($output->thumbnails as $remote_thumbnail) {
          $image = array_shift($remote_thumbnail->images);
          // Save the thumbnail.
          $thumb_filename = basename(substr($image->url, 0, strpos($image->url, '?')));
          file_put_contents($path . '/' . $post->job->id . $remote_thumbnail->label . $thumb_filename, file_get_contents($image->url));
          $thumbnail = array(
            'label' => $remote_thumbnail->label,
            'dimensions' => $image->dimensions,
            'format' => $image->format,
            'reference' => $cdn->getPath($zencoder->generatePath($local_media), FALSE) . '/' .  $post->job->id . $remote_thumbnail->label . $thumb_filename,
          );

          $thumbnails[] = $thumbnail;
        }

        // Metadata including everything Zencoder sends us.
        $metadata = array(
          'reference' => $cdn->getPath($zencoder->generatePath($local_media), FALSE) . '/' . $video_filename,
          'label' => $output->label,
          'format' => $output->format,
          'frame_rate' => $output->frame_rate,
          'length' => $output->duration_in_ms / 1000,
          'audio_sample_rate' => $output->audio_sample_rate,
          'audio_bitrate_in_kbps' => $output->audio_bitrate_in_kbps,
          'audio_codec' => $output->audio_codec,
          'height' => $output->height,
          'width' => $output->width,
          'file_size_in_bytes' => $output->file_size_in_bytes,
          'video_codec' => $output->video_codec,
          'total_bitrate_in_kbps' => $output->total_bitrate_in_kbps,
          'channels' => $output->channels,
          'video_bitrate_in_kbps' => $output->video_bitrate_in_kbps,
          'thumbnails' => $thumbnails
        );
        $transcoded[] = $metadata;
      }

      // Setup metadata.
      $local_media->setProviderMetadata($transcoded);
      $local_media->setLength($post->input->duration_in_ms / 1000);
      $local_media->setWidth($post->input->width);
      $local_media->setHeight($post->input->height);
      $local_media->setUpdatedAt(new \DateTime);
      $local_media->setAuthorName(NULL);
      $local_media->setProviderStatus(MediaInterface::STATUS_OK);

      $mediaManager = $this->get("sonata.media.manager.media");
      $mediaManager->save($local_media);

      $status = TRUE;
    }

    $response = new Response(json_encode(array($status)));
    // JSON header.
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }
}
