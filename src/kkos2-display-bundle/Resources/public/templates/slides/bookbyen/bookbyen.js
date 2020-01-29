// Register the function, if it does not already exist.
if (!window.slideFunctions["bookbyen"]) {
  window.slideFunctions["bookbyen"] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: scope => {
      const slide = scope.ikSlide;
      let subslides = [];
      let num_subslides = 0;
      if (slide.external_data && slide.external_data.sis_data_slides) {
        subslides = slide.external_data.sis_data_slides;
        num_subslides = slide.external_data.sis_data_num_slides;
      }
      const slide_duration = slide.options.sis_subslide_duration
        ? slide.options.sis_subslide_duration
        : 10;
      window.slidesInSlides.setup(
        scope,
        subslides,
        num_subslides,
        slide_duration
      );

      scope.totalSubslides = num_subslides;
      scope.useFields = slide.options.bookbyen.useFields;
      scope.ikSlide.kffLogo = slide.server_path + "/bundles/kkos2displayintegration/assets/img/kbh-logo.png";
      scope.ratio = window.kkSlideRatio.getRatio();

      function setTime() {
        const now = new Date();
        scope.hourNow = now.getHours();
        scope.minuteNow = now.getMinutes();
      }
      setTime();
      setInterval(setTime, 60000);

    },

    /**
     * Run the slide.
     *
     * @param slide
     *   The slide.
     * @param region
     *   The region to call when the slide has been executed.
     */
    run: (slide, region) => {
      window.slidesInSlides.run(slide, region);
    }
  };
}
