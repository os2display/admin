/**
 * Base slide, that just displays for slide.duration, then calls the callback.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['ding-events']) {
  window.slideFunctions['ding-events'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupDingEventsSlide(scope) {
      var slide = scope.ikSlide;

      if (slide.external_data && slide.external_data.events) {
        // Number of "Lines" we can show pr slide. A lines does not have to
        // excatly corrospond to an actual Em on the slide, all we need is some
        // way of expressing that some parts of the content will take up more
        // vertical space than other.
        var linesPrSlide = 8;
        var linePrGroup = 1.2;
        var linePrEvent = 1.0;


        // Build up the slides we want to display. We do this by looping trough
        // all available events, calculating how much space we have left on the
        // slide, when we run out, we add a new slide and continue on.
        // All events are grouped under a date-headline, when we hit the next
        // date we'll also need to add a new grouping.

        // Setup the initial slide with an empty group.
        var event_slides = [];
        var current_slide = -1;
        var current_group = -1;
        var events_added = 0;
        var current_group_date = null;
        var lines_used = 0;
        var max_events = (slide.options.max_events && slide.options.max_events > 0) ? slide.options.max_events : 999999;
        if (slide.external_data.events && slide.external_data.events.length > 0) {

          // Loop over events and assign them to groups and slides.
          for (var i = 0, len = slide.external_data.events.length; i < len; i++) {
            var event = slide.external_data.events[i];

            // We have 3 actions we can perform
            // 1: Add a new slide
            // 2: Add a new group (date headline followed by events for that day)
            // 3: Add a slide
            //
            // We always perform (3) as the last step, but first we have to
            // determine whether we need to do 1 and 2 first.

            // The following two flags determines whether to carry out action 1/2
            var addNewSlide = false;
            var addNewGroup = false;

            // The following checks sets the flags if nessecary, to keep the
            // checks simple some of them may overlap. Note that adding a slide
            // will implicitly add a new group.

            // Init condition - we don't have a slide, add it.
            if (current_slide === -1) {
              addNewSlide = true;
            }

            // If we've run out of space on the slide and can't add a new event
            // we'll need a new slide.
            if (lines_used + linePrEvent > linesPrSlide) {
              addNewSlide = true;
            }

            // If the current groups date does not match the event, we need a
            // new group (it will inherit its date from the current event).
            if (current_group_date !== event.date) {
              addNewGroup = true;
            }

            // If we need a new group, and it + the event will exceed what space
            // we have left, we need a new slide
            if (addNewGroup && (lines_used + linePrGroup + linePrEvent > linesPrSlide)) {
              addNewSlide = true;
            }

            // All checks done, now carry out any required actions.
            if (addNewSlide) {
              // Add a new slide with an empty group-array.
              event_slides.push([]);
              current_slide++;

              // New slide, so we reset the vertical space used.
              lines_used = 0;

              // Reset group count, and queue up a new group-insertion.
              current_group = -1;
              addNewGroup = true;
            }

            if (addNewGroup) {
              // Add a new group using the current events date and setup an
              // empty list of events.
              event_slides[current_slide].push( {
                headline: event.date_headline,
                date: event.date,
                events: []
              });
              current_group++;
              current_group_date = event.date;
              lines_used += linePrGroup;
            }

            // We now have a slide, group, and enough vertical space to add
            // our event, so lets do it.
            event_slides[current_slide][current_group].events.push(event);
            lines_used += linePrEvent;
            events_added++;
            if (events_added >= max_events) {
              break;
            }
          }
        }

        slide.event_settings = {
          // Current slide being displayed, used by angular as index to find
          // the slide
          currentSlide: 0,
          event_slides: event_slides
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
    run: function runDingEventsSlide(slide, region) {
      region.itkLog.info("Running ding events slide: " + slide.title);
      // reset slide-count.
      slide.event_settings.currentSlide = 0;
  
      var slide_duration = slide.options.duration ? slide.options.duration : 15;

      // Check that external_data exists, if not stay on for one duration and
      // continue to next slide.
      if (!slide.event_settings || !slide.event_settings.event_slides || slide.event_settings.event_slides <= 0) {
        region.$timeout(function() {
          region.nextSlide();
        }, slide_duration * 1000);
        return;
      }

      /**
       * Iterate through event slides.
       */
      var dingEventTimeout = function () {
        region.$timeout(function () {
          // If we've reached the end, go to next (real) slide.
          if (slide.event_settings.currentSlide + 1 >= slide.event_settings.event_slides.length) {
            region.nextSlide();
          }
          else {
            // We have more, iterate to the next (event) slide.
            slide.event_settings.currentSlide++;
            dingEventTimeout();
          }
        }, slide_duration * 1000);
      };

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
