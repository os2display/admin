/**
 * Calendar slide Dokk1.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['calendar-single-day-dokk1']) {
  window.slideFunctions['calendar-single-day-dokk1'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupCalendarSingleDaySlide(scope) {
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
        fontsize: slide.options.fontsize * (scope.scale ? scope.scale : 1.0)+ "px"
      };

      // Set the responsive font size if it is needed.
      if (slide.options.responsive_fontsize) {
        scope.theStyle.responsiveFontsize = slide.options.responsive_fontsize * (scope.scale ? scope.scale : 1.0)+ "vw";
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
    run: function runCalendarSingleDaySlide(slide, region) {
      region.itkLog.info("Running calendar-single-day-dokk1 slide: " + slide.title);

      var duration = slide.duration !== null ? slide.duration : 15;

      var bookedRegex = /\(optaget\)/i;

      if (slide.external_data) {
        var now = new Date();
        now = now.getTime();
        var start = new Date();
        start.setHours(0,0,0);
        start = start.getTime();
        var end = new Date();
        end.setHours(23,59,59);
        end = end.getTime();

        var arr = [];

        for (var i = 0; i < slide.external_data.length; i++) {
          if (slide.external_data[i].end_time * 1000 > now && slide.external_data[i].start_time * 1000 <= end) {
            var booking = angular.copy(slide.external_data[i]);
            if (booking.start_time * 1000 < start) {
              booking.start_time = parseInt(start / 1000);
            }
            if (booking.end_time * 1000 > end) {
              booking.end_time = parseInt(end / 1000);
            }

            // Remove all (liste) from the event_name
            booking.event_name = booking.event_name.split('(liste)').join('');

            // Replace the event_name with Optaget if it contains the (optaget)
            if (bookedRegex.test(booking.event_name)) {
              booking.event_name = 'Optaget';
            }

            arr.push(booking);
          }
        }

        slide.selected_data = arr;
      }

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