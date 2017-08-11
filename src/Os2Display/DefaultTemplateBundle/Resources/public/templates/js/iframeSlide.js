/**
 * Base slide, that just displays for slide.duration, then calls the callback.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['iframe']) {
  window.slideFunctions['iframe'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupIframeSlide(scope) {
      var slide = scope.ikSlide;

      // Last time the slide was refreshed.
      slide.lastRefresh = 0;

      // Return af new refreshed source, with a 5 minutes interval.
      slide.getRefreshedSource = function() {
        if (slide.options.disable_update) {
          return slide.options.source;
        }

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
     * @param region
     *   The region object.
     */
    run: function runIframeSlide(slide, region) {
      region.itkLog.info("Running iframe slide: " + slide.title);

      var duration = slide.duration !== null ? slide.duration : 15;

      // Wait fadeTime before start to account for fade in.
      region.$timeout(function () {
        // Set the progress bar animation.
        region.progressBar.start(duration);

        // Wait for slide duration, then show next slide.
        // + fadeTime to account for fade out.
        region.$timeout(function () {
          region.nextSlide();
        }, duration * 1000 + region.fadeTime);
      }, region.fadeTime);
    }
  };
}