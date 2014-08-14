/**
 * Media controller. Controls media editing functions.
 */
ikApp.controller('MediaEditController', function($scope, $location, $routeParams, mediaFactory) {

  /**
   * Scope setup
   */

  mediaFactory.getImage($routeParams.id).then(function(data) {
    $scope.image = data;

    if ($scope.image === {}) {
      $location.path('/media-overview');
    }
  });

  $scope.delete = function(id) {
    mediaFactory.deleteImage($scope.image.media.id);
    $location.path('/media-overview');
  };
});