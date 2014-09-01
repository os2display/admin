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
    mediaFactory.deleteImage($scope.image.id);
    $location.path('/media-overview');
  };


  /**
   * Sets the correct local path to the video
   */
  $scope.videoPath = function(element, browser) {
    var filepath = '';
    if (element.provider_status === '1') {
      filepath = '/uploads/media/default/0001/01/' + element.provider_reference;
    }
    console.log(element);
    return filepath;
  }
});