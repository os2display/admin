/**
 * TODO
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['kkevents']) {
  console.log('jeg virker vist ikke endnu?')
    window.slideFunctions['kkevents'] = {
        /**
         * Setup the slide for rendering.
         * @param scope
         *   The slide scope.
         */
        setup: function setupKkeventsSlide(scope) {
            console.log('he');
            var slide = scope.ikSlide;

            // Set currentLogo.
            slide.currentLogo = slide.logo;
            slide.currentImage = slide.media[0] && slide.media[0].image;

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
        run: function runEventSlide(slide, region) {
            // Log slide-info before starting up.
            region.itkLog.info("Running Event: " + slide.title);
            if (slide.options.feed) {
                if (slide.options.feed.library) {
                    region.itkLog.info("- library nid: " + slide.options.feed.library);
                }
            }

            // Get the strings we're going to display on the slide.
            if (slide.external_data.intervalTexts) {
                slide.intervalTexts = slide.external_data.intervalTexts;
            }

            if (slide.external_data.date_headline) {
                slide.date_headline = slide.external_data.date_headline;
            }

            // Wait fadeTime before start to account for fade in.
            var duration = slide.duration !== null ? slide.duration : 15;
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
