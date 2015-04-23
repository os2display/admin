/**
 * @file
 * Contains the admin sharing controller.
 */

/**
 * Admin sharing controller.
 */
angular.module('ikApp').controller('AdminSharingController', ['$scope', 'sharedChannelFactory', 'itkLogFactory',
  function ($scope, sharedChannelFactory, itkLogFactory) {
    'use strict';

    $scope.saving = false;

    $scope.availableIndexes = [];
    sharedChannelFactory.getAvailableIndexes().then(
      function success(data) {
        data.forEach(function (element) {
          // Only include shared indexes.
          if (element.tag === 'shared') {
            $scope.availableIndexes.push(element);
          }
        });
      },
      function error(reason) {
        itkLogFactory.error('Hentning af tilgængelige delingsindeks fejlede.', reason);
      }
    );
    $scope.chosenIndexes = [];
    sharedChannelFactory.getSharingIndexes().then(
      function success(data) {
        $scope.chosenIndexes = data;
      },
      function error(reason) {
        itkLogFactory.error('Hentning af valgte delingsindeks fejlede.', reason);
      }
    );

    $scope.save = function () {
      $scope.saving = true;
      sharedChannelFactory.saveSharingIndexes($scope.chosenIndexes).then(
        function success() {
          itkLogFactory.info('Delingsindeks gemt', 3000);
          $scope.saving = false;
        },
        function error(reason) {
          itkLogFactory.error('Delingsindeks blev ikke gemt', reason);
          $scope.saving = false;
        }
      );
    }
  }
]);