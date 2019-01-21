if (!window.slidesInSlides) {
  window.slidesInSlides = {};
}
if (!window.slidesInSlides.setup) {
  /**
   * Setup the slide for rendering.
   *
   * @param scope
   *   The slide scope.
   * @param subslides
   *   The slides in slide.
   * @param num_subslides
   *   The number of slides in the slide.
   * @param subslide_duration
   *   How long to display each subslide.
   */
  window.slidesInSlides.setup = function (scope, subslides, num_subslides, subslide_duration) {
    scope.ikSlide.data = {
      currentSlide: 0,
      subslides: subslides,
      num_subslides: num_subslides,
      subslide_duration: subslide_duration,
    };

  };
}

if (!window.slidesInSlides.run) {
  /**
   * Run the slide.
   *
   * @param slide
   *   The slide.
   * @param region
   *   The region to call when the slide has been executed.
   */
  window.slidesInSlides.run = function (slide, region) {
    // Experience has shown that we can't be certain that all our data is
    // present, so we'll have to be careful verify presence before accessing
    // anything.
    if (!slide.options || !slide.data || !slide.data.subslides) {
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

    var slide_duration = slide.data.subslide_duration ? slide.data.subslide_duration : 15;

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
          console.log('Advancing to subslide ' + (1 + slide.data.currentSlide) + ' of ' + slide.data.num_subslides);
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
  };
}
