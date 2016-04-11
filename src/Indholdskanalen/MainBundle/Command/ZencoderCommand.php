<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indholdskanalen\MainBundle\Command;

use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronCommand
 *
 * @package Indholdskanalen\MainBundle\Command
 */
class ZencoderCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this
      ->setName('ik:zencoder')
      ->setDescription('Job queue callback')
      ->addArgument(
        'json',
        InputArgument::OPTIONAL,
        'Zencoder JSON'
      );
  }

  /**
   * Executes the command
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|null|void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $logger = $this->getContainer()->get('monolog.logger.zencoder');

    $json = $input->getArgument('json');
    $post = json_decode($json);

    // Log that data have been return form zencoder.
    $logger->info($post->job->pass_through);

    // Find the correct media.
    $media_manager = $this->getContainer()->get('sonata.media.manager.media');
    $local_media = $media_manager->findOneBy(array('id' => $post->job->pass_through));

    if ($local_media) {
      $cdn = $this->getContainer()->get('sonata.media.cdn.server');
      $zencoder = $this->getContainer()->get('sonata.media.provider.zencoder');
      $root = $this->getContainer()->get('kernel')->getRootDir() . '/../web';
      $media_url_path = $cdn->getPath($zencoder->generatePath($local_media), FALSE);
      $path = $root . parse_url($media_url_path, PHP_URL_PATH);

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
          'thumbnails' => $thumbnails,
        );
        $transcoded[] = $metadata;
      }

      // Setup metadata.
      $local_media->setProviderMetadata($transcoded);
      $local_media->setLength($post->input->duration_in_ms / 1000);
      $local_media->setWidth($post->input->width);
      $local_media->setHeight($post->input->height);
      $local_media->setUpdatedAt(new \DateTime());
      $local_media->setAuthorName(NULL);
      $local_media->setProviderStatus(MediaInterface::STATUS_OK);

      $media_manager->save($local_media);
    }
    else {
      $logger->error('Callback call for media not found in DB (mediaId: ' . $post->job->pass_through .', jobId: '. $post->job->id .')');
    }
  }
}