/**
 * @file
 * Images controller handles the media overview page.
 */
ikApp.controller('MediaOverviewController', function ($scope, $location) {
  $scope.$on('mediaOverview.selectImage', function(event, image) {
    $location.path('/media/' + image.id);
  });

  // Mouse hover on image.
  $scope.hovering = false;


  /**
   * Adds hover overlay on media elements.
   */
  $scope.mouseHover = function(state) {
    if(state > 0) {
      $scope.hovering = state;
    } else {
      $scope.hovering = false;
    }
  }


  /**
   * Sets the path for the thumbnail.
   */
  $scope.videoThumb = function(element) {
    // Default image while image is encoding.
    var filepath = 'images/encoding.png';

    // Thumbnail when encoding has finished.
    if (element.provider_status === 1) {
      filepath = 'images/logo.png';
    }

    return filepath;
  }
});
