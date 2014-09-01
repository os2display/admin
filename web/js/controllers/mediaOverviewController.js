/**
 * Media controller handles the media overview page.
 */
ikApp.controller('MediaOverviewController', function ($scope, $location) {
  // Register event listener for the select image event.
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
});
