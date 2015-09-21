/**
 * Base slide, that just displays for slide.duration, then calls the callback.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['iframe']) {
  window.slideFunctions['iframe'] = {
    /**
     * Setup the slide for rendering.
     * @param slide
     *   The slide.
     * @param scope
     *   The slide scope.
     */
    setup: function setupIframeSlide(slide, scope) {
      // Last time the slide was refreshed.
      slide.lastRefresh = 0;

      // Return af new refreshed source, with a 5 minutes interval.
      slide.getRefreshedSource = function() {
        var date = (new Date()).getTime();
        if (date - slide.lastRefresh > 300000) {
          slide.lastRefresh = date;
        }

        // Make sure path parameters are not overridden.
        if (slide.options.source.indexOf('?') > 0) {
          return slide.options.source + "&ikrefresh=" + slide.lastRefresh;
        }
        else {
          return slide.options.source + "?ikrefresh=" + slide.lastRefresh;
        }
      };
    },

    /**
     * Run the slide.
     *
     * @param slide
     *   The slide.
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
    run: function runIframeSlide(slide, callback, $http, $timeout, $interval, $sce, itkLog, startProgressBar, fadeTime) {
      itkLog.info("Running iframe slide: " + slide.title);

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