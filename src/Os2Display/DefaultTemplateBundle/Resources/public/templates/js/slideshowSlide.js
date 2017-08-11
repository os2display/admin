/**
 * Slide show ls
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['slideshow']) {
  window.slideFunctions['slideshow'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupBaseSlide(scope) {
      var slide = scope.ikSlide;

      slide.getStyle = function getStyle(slide, media) {
        return slide.fadeStyle + media.animationStyle;
      };

      // Set currentLogo.
      slide.currentLogo = slide.logo;

      if (!slide.options.hasOwnProperty('duration')) {
        slide.options.duration = 15;
      }
      else {
        slide.options.duration = parseInt(slide.options.duration);
      }

      if (slide.options.fade === 'fade') {
        slide.fadeStyle = ' display: block; -webkit-transition: opacity 1s ease-in; -moz-transition: opacity 1s ease-in; -o-transition: opacity 1s ease-in; transition: opacity 1s ease-in;';
      }
      else {
        slide.fadeStyle = ' opacity: 1; display: block;';
      }

      for (var i = 0; i < slide.media.length; i++) {
        if (slide.options.hasOwnProperty('animation')) {
          var selectedStyle = null;
          var setRandomOrigin = false;

          if (slide.options.animation === 'zoom-in-middle') {
            selectedStyle = 0;
          }
          else if (slide.options.animation === 'zoom-out-middle') {
            selectedStyle = 1;
          }
          else if (slide.options.animation === 'random') {
            selectedStyle = Math.floor((Math.random() * 2));

            if (Math.floor((Math.random() * 2)) === 0) {
              setRandomOrigin = true;
            }
          }
          else if (slide.options.animation === 'zoom-in-random') {
            selectedStyle = 0;
            setRandomOrigin = true;
          }
          else if (slide.options.animation === 'zoom-out-random') {
            selectedStyle = 1;
            setRandomOrigin = true;
          }

          switch (selectedStyle) {
            case 0:
              slide.media[i].animationStyle = '-webkit-animation: ' + slide.options.duration + 's zoom-in-middle; animation: ' + slide.options.duration + 's zoom-in-middle; ';
              break;
            case 1:
              slide.media[i].animationStyle = '-webkit-animation: ' + slide.options.duration + 's zoom-out-middle; animation: ' + slide.options.duration + 's zoom-out-middle; ';
              break;
            default:
              slide.media[i].animationStyle = '';
              break;
          }

          if (setRandomOrigin) {
            var randomString = Math.floor((Math.random() * 100) + 1) + "% " + Math.floor((Math.random() * 100) + 1) + "%";
            var origin = slide.media[i].animationStyle +
              " transform-origin: " + randomString +  ";" +
              " -ms-transform-origin: " + randomString + ";" +
              " -webkit-transform-origin: " + randomString + "; ";

            slide.media[i].animationStyle = slide.media[i].animationStyle + origin;
          }
        }
        else {
          slide.media[i].animationStyle = '';
        }

        slide.media[i].style =  slide.media[i].animationStyle + slide.fadeStyle;
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
    run: function runSlideshowSlide(slide, region) {
      region.itkLog.info("Running slideshow slide: " + slide.title);

      /**
       * Go to next rss news.
       */
      var slideshowTimeout = function () {
        region.itkLog.info("imageEntry: " + slide.imageEntry);

        region.$timeout(function () {
          if (slide.imageEntry + 1 >= slide.media.length) {
            region.nextSlide();
          }
          else {
            slide.imageEntry++;
            slideshowTimeout();
          }
        }, slide.options.duration * 1000);
      };

      slide.imageEntry = 0;

      slideshowTimeout();

      // Wait fadeTime before start to account for fade in.
      region.$timeout(function () {
        // Set the progress bar animation.
        region.progressBar.start((slide.options.duration - 1) * slide.media.length);
      }, region.fadeTime);
    }
  };
}