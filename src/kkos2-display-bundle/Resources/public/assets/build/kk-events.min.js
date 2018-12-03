
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
      if (!slide.external_data || !slide.external_data.events || slide.external_data.events.length < 1) {
        return;
      }

      var events = slide.external_data.events;

      var currentSlide = -1;
      var eventSlides = [];
      events.forEach(function (event, i) {
        if (i % 3 === 0) {
          currentSlide++;
          eventSlides[currentSlide] = [];
        }
        eventSlides[currentSlide].push(event);
      });


      slide.event_settings = {
        // Current slide being displayed, used by angular as index to find
        // the slide
        currentSlide: 0,
        event_slides: eventSlides
      };

      // Setup the inline styling
      scope.theStyle = {
        width: "100%",
        height: "100%",
        fontsize: slide.options.fontsize * (scope.scale ? scope.scale : 1.0) + "px"
      };

      // // Set the responsive fontsize if it is needed.
      // if (slide.options.responsive_fontsize) {
      //   scope.theStyle.responsiveFontsize = slide.options.responsive_fontsize * (scope.scale ? scope.scale : 1.0) + "vw";
      // }
    },

    /**
     * Run the slide.
     *
     * @param slide
     *   The slide.
     * @param region
     *   The region to call when the slide has been executed.
     */
    run: function runDingEventsSlide(slide, region) {
      // Experience has shown that we can't be certain that all our data is
      // present, so we'll have to be careful verify presence before accessing
      // anything.
      if (!slide.options || !slide.external_data || !slide.external_data.events || slide.external_data.events.length <= 0 || !slide.event_settings) {
        // Go straight to the next slide if we're missing something. For now we
        // simply assume that we have a "next" to go to, if not, we're going
        // to loop real fast.

        // In some situations the data is just about to be ready. Skipping the
        // slide once and letting us get control back right away gives us the
        // time we need.
        if (!slide.loop_throttle) {
          region.itkLog.info("Skipping to buy time for data ...");
          slide.loop_throttle = 1;
          return;
        }

        // We tried the skip, did not work, continue to next slide.
        region.itkLog.info("No data for slide, skipping");

        region.nextSlide();
        return;
      }
      // Reset throttle in case we where successful.
      slide.loop_throttle = false;

      var slide_duration = slide.options.rss_duration ? slide.options.rss_duration : 15;

      /**
       * Iterate through event slides.
       */
      var dingEventTimeout = function () {
        region.$timeout(function () {
          // If we've reached the end, go to next (real) slide.
          if (slide.event_settings.currentSlide + 1 >= slide.event_settings.event_slides.length) {
            region.nextSlide();
          } else {
            // We have more, iterate to the next (event) slide.
            slide.event_settings.currentSlide++;
            dingEventTimeout();
          }
        }, slide_duration * 1000);
      };

      // reset slide-count.
      slide.event_settings.currentSlide = 0;

      // Trigger initial sleep an subsequent advance of slide.
      dingEventTimeout();

      // Wait fadeTime before start to account for fade in.
      region.$timeout(function () {
        // Set the progress bar animation.
        var duration = slide_duration * slide.event_settings.event_slides.length;
        region.progressBar.start(duration);
      }, region.fadeTime);

    }
  };
}
