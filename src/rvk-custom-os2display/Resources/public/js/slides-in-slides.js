// TODO. This file should just be included in other files with gulp or similar.
// There is no way to add more than one file pr slide, so reuse needs to get
// a little creative. Ideally, this file should live in the
// os2display-slide-tools repo and get included from there, but we have not yet
// landed how bundles are included.

if (!window.slidesInSlides) {
    window.slidesInSlides = {};
}
if (!window.slidesInSlides.setup) {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    window.slidesInSlides.setup = function(scope) {
        var slide = scope.ikSlide;
        if (!slide.external_data || !slide.external_data.sis_data_slides || slide.external_data.sis_data_slides < 1) {
            return;
        }

        slide.data = {
            // Current slide being displayed, used by angular as index to find
            // the slide
            currentSlide: 0,
            subslides: slide.external_data.sis_data_slides,
            num_subslides: slide.external_data.sis_data_num_slides,
            items_pr_slide: slide.external_data.sis_data_items_pr_slide
        };


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
            scope.theStyle.eventBoxHeight = (availableSpace / slide.external_data.events_pr_slide) + 'vh';
        }

        // Set the responsive fontsize if it is needed.
        if (slide.options.responsive_fontsize) {
            // scope.theStyle.responsiveFontsize = slide.options.responsive_fontsize * (scope.scale ? scope.scale : 1.0) + "vw";
        }
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
        if (!slide.options || !slide.data.subslides || slide.data.num_subslides < 1) {
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

        var slide_duration = slide.options.sis_subslide_duration ? slide.options.sis_subslide_duration : 15;

        /**
         * Iterate through event slides.
         */
        var eventSlideTimeout = function () {
            region.$timeout(function () {
                // If we've reached the end, go to next (real) slide.
                if (slide.data.currentSlide+1 >= slide.data.num_subslides) {
                    region.nextSlide();
                } else {
                    // We have more, iterate to the next (event) slide.
                    slide.data.currentSlide++;
                    console.log('Advancing to sublide ' + (1 + slide.data.currentSlide) + ' of ' + slide.data.num_subslides);
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


// Register the function, if it does not already exist.
if (!window.slideFunctions['bib-events']) {
    window.slideFunctions['bib-events'] = {

        setup: function setupEventsSlide(scope) {
            window.slidesInSlides.setup(scope);
        },

        run: function runEventsSlide(slide, region) {
            window.slidesInSlides.run(slide, region);
        }
    }
}
