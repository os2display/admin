// Register the function, if it does not already exist.
if (!window.slideFunctions["kk-articles"]) {

   window.slideFunctions["kk-articles"] = {
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

       scope.ikSlide.data = {
         currentSlide: 0,
         subslides: subslides,
         num_subslides: num_subslides,
       };

       scope.ratio = window.kkSlideRatio.getRatio();
     },

    run: (slide, region) => {
      window.slidesInSlides.run(slide, region);
    }
   };

}
