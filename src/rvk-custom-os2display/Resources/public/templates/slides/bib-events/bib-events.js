if (!window.slideFunctions['bib-events']) {
  window.slideFunctions['bib-events'] = {

    setup: function setupBibEvents(scope) {
      var slide = scope.ikSlide;
      var subslides = [];
      var num_subslides = 0;
      if (slide.external_data && slide.external_data.sis_data_slides) {
        subslides = slide.external_data.sis_data_slides;
        num_subslides = slide.external_data.sis_data_num_slides;
      }

      var slide_duration = slide.options.subslide_duration ? slide.options.subslide_duration : 10;
      window.slidesInSlides.setup(scope, subslides, num_subslides, slide_duration);

      // Setup the inline styling
      scope.theStyle = {
        width: "100%",
        height: "100%",
        fontsize: slide.options.fontsize * (scope.scale ? scope.scale : 1.0) + "px",
      };

      if (slide.options.use_box_heights) {
        var vhMarginAroundEachEvent = ((slide.data.items_pr_slide - 1) * 2);
        var vhMarginPage = 10;
        var availableSpace = 100 - (vhMarginAroundEachEvent + vhMarginPage);
        scope.theStyle.eventBoxHeight = (availableSpace / slide.external_data.sis_data_items_pr_slide) + 'vh';
      }
    },

    run: function runEventsSlide(slide, region) {
      window.slidesInSlides.run(slide, region);
    }
  };
}
