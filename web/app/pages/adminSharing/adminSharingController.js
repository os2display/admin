/**
 * @file
 * Contains the admin sharing controller.
 */

/**
 * Admin sharing controller.
 */
angular.module('ikApp').controller('AdminSharingController', ['$scope', 'sharedChannelFactory',
  function ($scope, sharedChannelFactory) {
    $scope.saving = false;

    $scope.availableIndexes = [];
    sharedChannelFactory.getAvailableIndexes().then(
      function (data) {
        data.forEach(function (element) {
          // Only include shared indexes.
          if (element.tag === 'shared') {
            $scope.availableIndexes.push(element);
          }
        });
      }
    );
    $scope.chosenIndexes = [];
    sharedChannelFactory.getSharingIndexes().then(
      function (data) {
        $scope.chosenIndexes = data;
      }
    );

    $scope.save = function () {
      $scope.saving = true;
      sharedChannelFactory.saveSharingIndexes($scope.chosenIndexes).then(
        function (data) {
          $scope.saving = false;
        },
        function (reason) {
          $scope.saving = false;
        }
      );
    }
  }
]);