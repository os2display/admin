/**
 * Instagram slide, that just displays for slide.duration, then calls the callback.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['instagram']) {
  window.slideFunctions['instagram'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupInstagramSlide(scope) {
      var slide = scope.ikSlide;

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
    run: function runInstagramSlide(slide, region) {
      // Reset instagram show.
      slide.instagramEntry = 0;

      region.itkLog.info("Running instagram slide: " + slide.title);

      // Check that external_data exists.
      if (!slide.external_data) {
        region.nextSlide();

        return;
      }

      /**
       * Go to next instagram news.
       */
      var instagramTimeout = function instagramTimeout() {
        region.$timeout(function () {
          if (slide.instagramEntry + 1 >= slide.external_data.length) {
            region.nextSlide();
          }
          else {
            slide.instagramEntry++;
            instagramTimeout(slide);
          }
        }, slide.options.instagram_duration * 1000);
      };

      // Start the show
      instagramTimeout();

      // Wait fadeTime before start to account for fade in.
      region.$timeout(function () {
        // Set the progress bar animation.
        var duration = slide.options.instagram_duration * slide.external_data.length;
        region.progressBar.start(duration);
      }, region.fadeTime);
    }
  }
}
