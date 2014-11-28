/**
 * @file
 * Contains the admin sharing controller.
 */

/**
 * Admin sharing controller.
 */
ikApp.controller('AdminSharingController', ['$scope', 'sharedChannelFactory',
  function($scope, sharedChannelFactory) {
    $scope.availableIndexes = [];
    sharedChannelFactory.getAvailableIndexes().then(
      function(data) {
        $scope.availableIndexes = data;
      }
    );
    $scope.chosenIndexes = [];
    sharedChannelFactory.getSharingIndexes().then(
      function(data) {
        $scope.chosenIndexes = data;
      }
    );

    $scope.save = function() {
      sharedChannelFactory.saveSharingIndexes($scope.chosenIndexes).then(
        function(data) {
          console.log("fisk");
        }
      );
    }
  }
]);