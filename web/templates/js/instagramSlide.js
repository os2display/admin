/**
 * Instagram slide, that just displays for slide.duration, then calls the callback.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['instagram']) {
  window.slideFunctions['instagram'] = {
    /**
     * Setup the slide for rendering.
     * @param slide
     *   The slide.
     * @param scope
     *   The slide scope.
     */
    setup: function setupInstagramSlide(slide, scope) {
      // Only show first image in array.
      if (slide.media_type === 'image' && slide.media.length > 0) {
        slide.currentImage = slide.media[0].image;
      }

      // Set currentLogo.
      slide.currentLogo = slide.logo;

      // Setup the inline styling
      // @TODO: Is there and remove function that cleans up the scope? Memory
      //        leak style?
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
     * @param scope
     *   The region scope
     * @param region
     *   The region to call when the slide has been executed.
     * @param $http
     *   Access to $http
     * @param $timeout
     *   Access to $timeout
     * @param $interval
     *   Access to $interval
     * @param $sce
     *   Access to $sce
     * @param itkLog
     *   Access to itkLog
     * @param progressBar
     *   ProgressBar object.
     * @param fadeTime
     *   The fade time.
     */
    run: function runInstagramSlide(slide, scope, region, $http, $timeout, $interval, $sce, itkLog, progressBar, fadeTime) {
      itkLog.info("Running instagram slide: " + slide.title);

      /**
       * Go to next instagram news.
       */
      var instagramTimeout = function (slide) {
        $timeout(function () {
          if (slide.instagram.instagramEntry + 1 >= slide.options.instagram_number) {
            region.nextSlide();
          }
          else {
            slide.instagram.instagramEntry++;
            instagramTimeout(slide);
          }
        }, slide.options.instagram_duration * 1000);
      };

      // Get the feed
      $http.jsonp(
        "https://api.instagram.com/v1/tags/" + slide.options.instagram_hashtag + "/media/recent?callback=JSON_CALLBACK&client_id=6dd7e66940864efebcfe9a09a920ad8d&count=" + slide.options.instagram_number)
        .success(function (data) {

          if (!slide.hasOwnProperty('instagram')) {
            slide.instagram = {
              feed: []
            };
          }

          data.data.forEach(function (entry, index, data) {
            var element = {};
            // Check if the instagram entry has a caption.
            element.text = entry.caption !== null ? entry.caption.text : '';
            element.user = {
              username: entry.user.username,
              image: entry.user.profile_picture
            };

            element.image = entry.images.standard_resolution.url.replace("/s640x640", "");

            slide.instagram.feed.push(element);
          });

          slide.instagram.instagramEntry = 0;

          instagramTimeout(slide);

          // Set the progress bar animation.
          var dur = slide.options.instagram_duration * slide.options.instagram_number - 1;
          progressBar.start(dur);
        })
        .error(function (message) {
          itkLog.error(message);
          if (slide.instagram.feed && slide.instagram.feed.length > 0) {
            slide.instagram.instagramEntry = 0;
            instagramTimeout(slide);
          }
          else {
            // Go to next slide.
            // @TODO: If slide error why wait 5 sec?
            $timeout(region.nextSlide, 5000);
          }
        });
    }
  }
}
