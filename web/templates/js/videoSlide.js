/**
 * Video slide, that plays the video.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['video']) {
  window.slideFunctions['video'] = {
    /**
     * Setup the slide for rendering.
     * @param slide
     *   The slide.
     * @param scope
     *   The slide scope.
     */
    setup: function setupVideoSlide(slide, scope) {
      if (slide.media_type === 'video' && slide.media.length > 0) {
        // Set current video variable to path to video files.
        slide.currentVideo = {
          "mp4": slide.media[0].mp4,
          "ogg": slide.media[0].ogv,
          "webm": slide.media[0].webm
        };
      }
    },

    /**
     * Run the slide.
     *
     * @param slide
     *   The slide.
     * @param scope
     *   The region scope
     * @param callback
     *   The callback to call when the slide has been executed.
     * @param $http
     *   Access to $http
     * @param $timeout
     *   Access to $timeout
     * @param $interval
     *   Access to $interval
     * @param $sce
     *   Access to $sce
     * @param itkLog
     *   Access to itkLog
     * @param startProgressBar
     *   Function to start the progress bar.
     * @param fadeTime
     *   The fade time.
     */
    run: function runVideoSlide(slide, scope, callback, $http, $timeout, $interval, $sce, itkLog, startProgressBar, fadeTime) {
      itkLog.info("Running video slide: " + slide.title);

      /**
       * Helper function to update source for video.
       *
       * This is due to a memory leak problem in chrome.
       *
       * @param video
       *   The video element.
       * @param reset
       *   If true src is unloaded else src is set from data-src.
       */
      var updateVideoSources = function updateVideoSources(video, reset) {
        // Due to memory leak in chrome we change the src attribute.
        var sources = video.getElementsByTagName('source');
        for (var i = 0; i < sources.length; i++) {
          if (reset) {
            // @see http://www.attuts.com/aw-snap-solution-video-tag-dispose-method/ about the pause and load.
            video.pause();
            sources[i].setAttribute('src', '');
            video.load();
          }
          else {
            sources[i].setAttribute('src', sources[i].getAttribute('data-src'));
          }
        }
      };

      /**
       * Handle video error.
       *
       * @param event
       *   If defined it's a normal error event else it's offline down.
       */
      var videoErrorHandling = function videoErrorHandling(event) {
        if (event !== undefined) {
          // Normal javascript error event.
          event.target.removeEventListener(event.type, videoErrorHandling);
          itkLog.error('Network connection.', event);
        }
        else {
          itkLog.error('Unknown video network connection error.');
        }
        Offline.off('down');

        // Go to the next slide.
        callback();
      };

      // If media is empty go to the next slide.
      if (slide.media.length <= 0) {
        callback();
      }

      // Check if there is an internet connection.
      Offline.on('down', videoErrorHandling);
      Offline.check();
      if (Offline.state === 'down') {
        videoErrorHandling(undefined);
        return;
      }

      // Get hold of the video element.
      var video = document.getElementById('videoPlayer-' + slide.uniqueId);

      // Update video.
      updateVideoSources(video, false);

      // Add error handling.
      video.addEventListener('error', videoErrorHandling);

      // Reset video position to prevent flicker from latest playback.
      try {
        // Load video to ensure playback after possible errors from last playback. If not called
        // the video will not play.
        video.load();
        video.currentTime = 0;
      }
      catch (error) {
        itkLog.info('Video content might not be loaded, so reset current time not possible');

        // Use the error handling to get next slide.
        videoErrorHandling(undefined);
      }

      // Fade timeout to ensure video don't start before it's displayed.
      $timeout(function () {
        // Create interval to get video duration (ready state larger than one is meta-data loaded).
        var interval = $interval(function () {
          if (video.readyState > 0) {
            var duration = Math.round(video.duration);
            startProgressBar(duration);

            // Metadata/duration found stop the interval.
            $interval.cancel(interval);
          }
        }, 500);

        // Go to the next slide when video playback has ended.
        video.onended = function ended(event) {
          itkLog.info("Video playback ended.", event);
          $timeout(function () {
              scope.$apply(function () {
                // Remove error handling.
                video.removeEventListener('error', videoErrorHandling);
                Offline.off('down');

                // Remove video src.
                updateVideoSources(video, true);

                // Go to the next slide.
                callback();
              });
            },
            1000);
        };

        // Play the video.
        video.play();
      }, fadeTime);
    }
  };
}