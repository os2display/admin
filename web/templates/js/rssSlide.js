/**
 * Base slide, that just displays for slide.duration, then calls the callback.
 */

// Register the function, if it does not already exist.
if (!window.slideFunctions['rss']) {
  window.slideFunctions['rss'] = {
    /**
     * Setup the slide for rendering.
     * @param scope
     *   The slide scope.
     */
    setup: function setupRssSlide(scope) {
      var slide = scope.ikSlide;

      // Only show first image in array.
      if (slide.media_type === 'image' && slide.media.length > 0) {
        slide.currentImage = slide.media[0].image;
      }

      // Set currentLogo.
      slide.currentLogo = slide.logo;

      // Setup the inline styling
      scope.theStyle = {
        width: "100%",
        height: "100%",
        fontsize: slide.options.fontsize * (scope.scale ? scope.scale : 1.0)+ "px"
      };

      // Set the responsive fontsize if it is needed.
      if (slide.options.responsive_fontsize) {
        scope.theStyle.responsiveFontsize = slide.options.responsive_fontsize * (scope.scale ? scope.scale : 1.0)+ "vw";
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
    run: function runRssSlide(slide, region) {
      region.itkLog.info("Running rss slide: " + slide.title);

      /**
       * Go to next rss news.
       */
      var rssTimeout = function () {
        region.$timeout(function () {
          if (slide.rss.rssEntry + 1 >= slide.options.rss_number) {
            region.nextSlide();
          }
          else {
            slide.rss.rssEntry++;
            rssTimeout(slide);
          }
        }, slide.options.rss_duration * 1000);
      };

      // Get the feed
      region.$http.jsonp(
        '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=' + slide.options.rss_number + '&callback=JSON_CALLBACK&output=xml&q=' +
        encodeURIComponent(slide.options.source))
        .success(function (data) {
          // Make sure we do not have an error result from googleapis
          if (data.responseStatus !== 200) {
            region.itkLog.error(data.responseDetails, data.responseStatus);
            if (slide.rss && slide.rss.feed && slide.rss.feed.entries && slide.rss.feed.entries.length > 0) {
              slide.rss.rssEntry = 0;
              rssTimeout(slide);
            }
            else {
              // Go to next slide.
              // @TODO: Hardcode magic 5 sec timeout?
              region.$timeout(region.nextSlide, 5000);
            }
            return;
          }

          var xmlString = data.responseData.xmlString;
          slide.rss = {feed: {entries: []}};
          slide.rss.rssEntry = 0;

          slide.rss.feed.title = $sce.trustAsHtml($(xmlString).find('channel > title').text());

          $(xmlString).find('channel > item').each(function () {
            var entry = $(this);

            var news = {};

            news.title = $sce.trustAsHtml(entry.find('title').text());
            news.description = $sce.trustAsHtml(entry.find('description').text());
            news.date = new Date(entry.find('pubDate').text());

            slide.rss.feed.entries.push(news);
          });

          rssTimeout(slide);

          // Set the progress bar animation.
          var duration = slide.options.rss_duration * slide.options.rss_number - 1;
          region.progressBar.start(duration);
        })
        .error(function (message) {
          region.itkLog.error(message);
          if (slide.rss.feed && slide.rss.feed.entries && slide.rss.feed.entries.length > 0) {
            slide.rss.rssEntry = 0;
            rssTimeout(slide);
          }
          else {
            // Go to next slide.
            region.nextSlide();
          }
        });
    }
  }
}
