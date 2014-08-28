/**
 * @file
 * Images controller handles the media overview page.
 */
ikApp.controller('MediaOverviewController', function ($scope, $location) {
  $scope.$on('mediaOverview.selectImage', function(event, image) {
    $location.path('/media/' + image.id);
  });

  console.log($scope);
  // Mouse hover on image.
  $scope.hovering = false;

  $scope.mouseHover = function(state) {
    if(state > 0) {
      $scope.hovering = state;
    } else {
      $scope.hovering = false;
    }
  }
});
