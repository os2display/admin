/**
 * Image controller. Controls image editing functions.
 */
ikApp.controller('ImageController', function($scope, $location, $routeParams, imageFactory) {

   /**
   * Constructor.
   * Handles different settings of route parameters.
   */
  function init() {
       $scope.testing = "123";

    if (!$routeParams.id) {
      // If the ID is not set, forward to media overview
        //$location.path('/media');
    } else {

      if ($routeParams.id == null || $routeParams.id == undefined || $routeParams.id == '') {
        //$location.path('/media');
      } else {
        console.info($routeParams.id);
        // Get the slide from the backend.


        imageFactory.getImage($routeParams.id).then(function(data) {
          $scope.image = data;

          if ($scope.image === {}) {
            $location.path('/media');
          }

        });

      }
    }
  }

  init();

});