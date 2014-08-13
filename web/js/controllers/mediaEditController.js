/**
 * Image controller. Controls image editing functions.
 */
ikApp.controller('MediaEditController', function($scope, $location, $routeParams, mediaFactory) {
  // Get the slide from the backend.
  mediaFactory.getImage($routeParams.id).then(function(data) {
    $scope.image = data;

    if ($scope.image === {}) {
      $location.path('/media');
    }
  });
});