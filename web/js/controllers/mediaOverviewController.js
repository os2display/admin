/**
 * @file
 * Images controller handles the media overview page.
 */
ikApp.controller('MediaOverviewController', function ($scope, $location) {
  $scope.$on('mediaOverview.selectImage', function(event, image) {
    $location.path('/media/' + image.id);
  });
});
