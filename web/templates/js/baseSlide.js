/**
 * Base slide, that just displays for slide.duration, then calls the callback.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['base']) {
  window.slideFunctions['base'] = {
    /**
     * Setup the slide for rendering.
     * @param slide
     *   The slide.
     * @param scope
     *   The slide scope.
     */
    setup: function setupBaseSlide(slide, scope) {
      // Only show first image in array.
      if (slide.media_type === 'image' && slide.media.length > 0) {
        slide.currentImage = slide.media[0].image;
      }

      // Set currentLogo.
      slide.currentLogo = slide.logo;

      // Setup the inline styling
      scope.theStyle = {
        width: "100%",
        height: "100%",
        fontsize: slide.options.fontsize * (scope.scale ? scope.scale : 1.0)+ "px"
      };

      // Set the responsive fontsize if it is needed.
      if (slide.options.responsive_fontsize) {
        scope.theStyle.responsiveFontsize = slide.options.responsive_fontsize * (scope.scale ? scope.scale : 1.0)+ "vw";
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
     *   Function to start the progress bar
     * @param fadeTime
     *   The fade time
     */
    run: function runBaseSlide(slide, scope, callback, $http, $timeout, $interval, $sce, itkLog, startProgressBar, fadeTime) {
      itkLog.info("Running base slide: " + slide.title);

      var dur = slide.duration ? slide.duration : 5;

      // Wait fadeTime before start to account for fade in.
      $timeout(function () {
        // Set the progress bar animation.
        startProgressBar(dur);

        // Wait for slide duration, then show next slide.
        // + fadeTime to account for fade out.
        $timeout(function () {
          callback();
        }, dur * 1000 + fadeTime);
      }, fadeTime);
    }
  };
}