/**
 * Video display controller. Controls display of video templates.
 */
ikApp.controller('VideoDisplayController', function($scope, $sce) {
  // Set the iframe source
  $scope.trustSrc = function(slide) {
    var src = slide.options.youtubeUrl;
    if (src) {
      if (slide.options.isValid >= 0) {
        // Alter the youtube url, to reflect an embed code.
        src = src.replace("watch?v=", "embed/");

        // Add parameters. ("?" mark for first parameter)
        // Hide info.
        src = src + "?showinfo=0";

        //Hide controls.
        src = src + "&controls=0";

        //Hide youtube logo.
        src = src + "&modestbranding=1";

        //Hide dont play related videos at end.
        src = src + "&rel=0";

        // Escape the source.
        return $sce.trustAsResourceUrl(src);
      }
      else {
        // Provide no source for iframe.
        return '';
      }
    }
  }
});