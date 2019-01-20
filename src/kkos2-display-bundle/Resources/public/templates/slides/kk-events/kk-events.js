// Register the function, if it does not already exist.
if (!window.slideFunctions['kk-events']) {
  window.slideFunctions['kk-events'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupKkEventsSlide(scope) {
      var slide = scope.ikSlide;
      if (!slide.external_data || !slide.external_data.slides || slide.external_data.slides.length < 1) {
        return;
      }

      slide.data = {
        // Current slide being displayed, used by angular as index to find
        // the slide
        currentSlide: 0,
        subslides: slide.external_data.slides,
        num_subslides: slide.external_data.slides.length
      };

      // Setup the inline styling
      scope.theStyle = {
        width: "100%",
        height: "100%",
      };
    },

    /**
     * Run the slide.
     *
     * @param slide
     *   The slide.
     * @param region
     *   The region to call when the slide has been executed.
     */
    run: function runEventsSlide(slide, region) {
      // Experience has shown that we can't be certain that all our data is
      // present, so we'll have to be careful verify presence before accessing
      // anything.
      if (!slide.options || !slide.data || !slide.data.subslides || slide.data.num_subslides < 1) {
        // Go straight to the next slide if we're missing something. For now we
        // simply assume that we have a "next" to go to, if not, we're going
        // to loop real fast.

        // In some situations the data is just about to be ready. Skipping the
        // slide once and letting us get control back right away gives us the
        // time we need.
        if (!slide.loop_throttle) {
          region.itkLog.info("Skipping to buy time for slides in slide data ...");
          slide.loop_throttle = 1;
          return;
        }

        // We tried the skip, did not work, continue to next slide.
        region.itkLog.info("No data for slides in slide, skipping");

        region.nextSlide();
        return;
      }

      // Reset throttle in case we where successful.
      slide.loop_throttle = false;

      var slide_duration = slide.options.rss_duration ? slide.options.rss_duration : 15;

      /**
       * Iterate through event slides.
       */
      var eventSlideTimeout = function () {
        region.$timeout(function () {
          // If we've reached the end, go to next (real) slide.
          if (slide.data.currentSlide + 1 >= slide.data.num_subslides) {
            region.nextSlide();
          } else {
            // We have more, iterate to the next (event) slide.
            slide.data.currentSlide++;
            console.log('Advancing to sublide ' + (1 + slide.data.currentSlide) + ' of ' + slide.data.num_subslides);
            eventSlideTimeout();
          }
        }, slide_duration * 1000);
      };

      console.log('Slide has ' + slide.data.num_subslides + ' subslides');

      // reset slide-count.
      slide.data.currentSlide = 0;

      // Trigger initial sleep and subsequent advance of slide.
      eventSlideTimeout();

      // Wait fadeTime before start to account for fade in.
      region.$timeout(function () {
        // Set the progress bar animation.
        var duration = slide_duration * slide.data.num_subslides;
        region.progressBar.start(duration);
      }, region.fadeTime);
    }
  };
}
