<?php
/**
 * @file
 * This file is a part of the Indholdskanalen MainBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indholdskanalen\MainBundle\Command;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\RequestException;
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
      if (TRUE || $local_media->getProviderStatus() == MediaInterface::STATUS_PENDING) {
        $cdn = $this->getContainer()->get('sonata.media.cdn.server');
        $zencoder = $this->getContainer()->get('sonata.media.provider.zencoder');
        $root = $this->getContainer()->get('kernel')->getRootDir() . '/../web';

        $media_url_path = $cdn->getPath($zencoder->generatePath($local_media), FALSE);
        $path = $root . parse_url($media_url_path, PHP_URL_PATH);

        $metadata = array();

        // Handle all the trans-coded video files. There should be mp4, ogv and
        // webm encoded video files.
        foreach ($post->outputs as $file_metadata) {
          $video_filename = basename(substr($file_metadata->url, 0, strpos($file_metadata->url, '?')));

          // Try to download remote video file.
          try {
            $resource = fopen($path . '/' . $video_filename, 'w');
            $client = new Client();
            $client->get($file_metadata ->url, ['sink' => $resource])->send();
            fclose($resource);
          }
          catch (\ErrorException $exception) {
            $msg = 'Error exception (video file handling): ' . $exception->getMessage();
            $logger->error($msg);
            $output->writeln($msg);
            return -1;
          }
          catch (RequestException $exception) {
            $msg = 'Request exception (video download): ' . $exception->getMessage();
            $logger->error($msg);
            $output->writeln($msg);
            return -1;
          }

          // Thumbnails. We save the first thumbnail per output.
          $thumbnails = array();
          foreach ($file_metadata->thumbnails as $remote_thumbnail) {
            $image = array_shift($remote_thumbnail->images);
            $thumb_filename = basename(substr($image->url, 0, strpos($image->url, '?')));

            // Try to download remote image file.
            try {
              $thumb_filename = '/' .  $post->job->id . $remote_thumbnail->label . $thumb_filename;
              $resource = fopen($path . $thumb_filename, 'w');
              $client = new Client();
              $client->get($image->url, ['sink' => $resource])->send();
              fclose($resource);
            }
            catch (\ErrorException $exception) {
              $msg = 'Error exception (video file handling): ' . $exception->getMessage();
              $logger->error($msg);
              $output->writeln($msg);

              return -1;
            }
            catch (RequestException $exception) {
              fclose($resource);
              $msg = 'Request exception (image download): ' . $exception->getMessage();
              $logger->error($msg);
              $output->writeln($msg);

              return -1;
            }

            $thumbnails[] = array(
              'label' => $remote_thumbnail->label,
              'dimensions' => $image->dimensions,
              'format' => $image->format,
              'reference' => $cdn->getPath($zencoder->generatePath($local_media), FALSE) . $thumb_filename,
            );
          }

          // Metadata including everything Zencoder sends us.
          $metadata[] = array(
            'reference' => $cdn->getPath($zencoder->generatePath($local_media), FALSE) . '/' . $video_filename,
            'label' => $file_metadata->label,
            'format' => $file_metadata->format,
            'frame_rate' => $file_metadata->frame_rate,
            'length' => $file_metadata->duration_in_ms / 1000,
            'audio_sample_rate' => $file_metadata->audio_sample_rate,
            'audio_bitrate_in_kbps' => $file_metadata->audio_bitrate_in_kbps,
            'audio_codec' => $file_metadata->audio_codec,
            'height' => $file_metadata->height,
            'width' => $file_metadata->width,
            'file_size_in_bytes' => $file_metadata->file_size_in_bytes,
            'video_codec' => $file_metadata->video_codec,
            'total_bitrate_in_kbps' => $file_metadata->total_bitrate_in_kbps,
            'channels' => $file_metadata->channels,
            'video_bitrate_in_kbps' => $file_metadata->video_bitrate_in_kbps,
            'thumbnails' => $thumbnails,
          );
        }

        // Setup metadata.
        $local_media->setProviderMetadata($metadata);
        $local_media->setLength($post->input->duration_in_ms / 1000);
        $local_media->setWidth($post->input->width);
        $local_media->setHeight($post->input->height);
        $local_media->setUpdatedAt(new \DateTime());
        $local_media->setProviderStatus(MediaInterface::STATUS_OK);

        $media_manager->save($local_media);
      }
      else {
        $msg = 'Media already handled (mediaId: ' . $post->job->pass_through . ', jobId: ' . $post->job->id .')';
        $logger->error($msg);
        $output->writeln($msg);

        return -1;
      }
    }
    else {
      $msg = 'Callback call for media not found in DB (mediaId: ' . $post->job->pass_through . ', jobId: ' . $post->job->id .')';
      $logger->error($msg);
      $output->writeln($msg);

      return -1;
    }

    $msg = 'Video and thumbnails download completed (mediaId: ' . $post->job->pass_through . ', jobId: ' . $post->job->id .')';
    $logger->error($msg);
    $output->writeln($msg);
  }
}