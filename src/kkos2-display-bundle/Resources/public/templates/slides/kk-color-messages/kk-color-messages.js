// Register the function, if it does not already exist.
if (!window.slideFunctions['kk-color-messages']) {
  window.slideFunctions['kk-color-messages'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupColorMessages(scope) {
      var slide = scope.ikSlide;
      var subslides = [];
      var num_subslides = 0;
      if (slide.external_data && slide.external_data.messages) {
        subslides = slide.external_data.messages;
        num_subslides = slide.external_data.messages.length;
      }
      slide.currentLogo = slide.logo;

      var slide_duration = slide.options.slide_duration ? slide.options.slide_duration : 15;
      window.slidesInSlides.setup(scope, subslides, num_subslides, slide_duration);
    },

    /**
     * Run the slide.
     *
     * @param slide
     *   The slide.
     * @param region
     *   The region to call when the slide has been executed.
     */
    run: function runColorfulSlide(slide, region) {
      window.slidesInSlides.run(slide, region);
    }
  };
}
