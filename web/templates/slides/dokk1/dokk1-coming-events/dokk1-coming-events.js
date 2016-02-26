/**
 * Dokk1-coming-events slide.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['dokk1-coming-events']) {
  window.slideFunctions['dokk1-coming-events'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupBaseSlide(scope) {
      var slide = scope.ikSlide;

      // Only show first image in array.
      if (slide.media_type === 'image' && slide.media.length > 0) {
        slide.currentImage = slide.media[0].image;
      }
    },

    /**
     * Run the slide.
     *
     * @param slide
     *   The slide.
     * @param region
     *   The region object.
     */
    run: function runBaseSlide(slide, region) {
      region.itkLog.info("Running dokk1-coming-events slide: " + slide.title);

      var duration = slide.duration !== null ? slide.duration : 15;

      slide.eventDays = {};

      slide.external_data.forEach(function (element) {
        var day = region.$filter('date')(new Date(element.start_time * 1000), 'EEEE d. MMMM');

        if (!slide.eventDays.hasOwnProperty(day)) {
          slide.eventDays[day] = [];
        }

        slide.eventDays[day].push(element);
      });

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