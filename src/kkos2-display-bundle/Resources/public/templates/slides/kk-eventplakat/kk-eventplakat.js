
// Register the function, if it does not already exist.
if (!window.slideFunctions['kk-eventplakat']) {
  window.slideFunctions['kk-eventplakat'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupKkEventPlakatSlide(scope) {
      var slide = scope.ikSlide;
      var subslides = [];
      var num_subslides = 0;
      if (slide.external_data && slide.external_data.plakat_slides) {
        subslides = slide.external_data.plakat_slides;
        num_subslides = slide.external_data.plakat_slides.length;
      }
      var slide_duration = slide.options.rss_duration ? slide.options.rss_duration : 15;
      window.slidesInSlides.setup(scope, subslides, num_subslides, slide_duration);

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
    run: function runKkPlakatSlide(slide, region) {
      window.slidesInSlides.run(slide, region);
    }
  };
}
