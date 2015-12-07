/**
 * Base slide, that just displays for slide.duration, then calls the callback.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['rss']) {
  window.slideFunctions['rss'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupRssSlide(scope) {
      var slide = scope.ikSlide;

      // Only show first image in array.
      if (slide.media_type === 'image' && slide.media.length > 0) {
        slide.currentImage = slide.media[0].image;
      }

      if (slide.external_data && slide.external_data.feed) {
        slide.rss = {
          rssEntry: 0,
          numberOfSlidesToShow: slide.options.rss_number < slide.external_data.feed.length ?
            slide.options.rss_number :
            slide.external_data.feed.length
        };
      }

      // Set currentLogo.
      slide.currentLogo = slide.logo;

      // Setup the inline styling
      scope.theStyle = {
        width: "100%",
        height: "100%",
        fontsize: slide.options.fontsize * (scope.scale ? scope.scale : 1.0) + "px"
      };

      // Set the responsive fontsize if it is needed.
      if (slide.options.responsive_fontsize) {
        scope.theStyle.responsiveFontsize = slide.options.responsive_fontsize * (scope.scale ? scope.scale : 1.0) + "vw";
      }
    },

    /**
     * Run the slide.
     *
     * @param slide
     *   The slide.
     * @param region
     *   The region to call when the slide has been executed.
     */
    run: function runRssSlide(slide, region) {
      region.itkLog.info("Running rss slide: " + slide.title);

      // Check that external_data exists.
      if (!slide.external_data || !slide.external_data.feed || slide.external_data.feed.length <= 0) {
        region.nextSlide();

        return;
      }

      // Allow html in description
      slide.external_data.feed.forEach(function(element) {
        if (!element.hasOwnProperty('safe_description')) {
          element.safe_description = region.$sce.trustAsHtml(element.description);
        }
      });

      /**
       * Go to next rss news.
       */
      var rssTimeout = function () {
        region.$timeout(function () {
          if (slide.rss.rssEntry + 1 >= slide.rss.numberOfSlidesToShow) {
            region.nextSlide();
          }
          else {
            slide.rss.rssEntry++;
            rssTimeout();
          }
        }, slide.options.rss_duration * 1000);
      };

      slide.rss.rssEntry = 0;

      rssTimeout();

      // Wait fadeTime before start to account for fade in.
      region.$timeout(function () {
        // Set the progress bar animation.
        var duration = slide.options.rss_duration * slide.rss.numberOfSlidesToShow;
        region.progressBar.start(duration);
      }, region.fadeTime);
    }
  }
}
