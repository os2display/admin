/**
 * @file
 * Contains the admin sharing controller.
 */

/**
 * Admin sharing controller.
 */
angular.module('ikApp').controller('AdminSharingController', ['busService', 'sharedChannelFactory', '$scope',
  function (busService, sharedChannelFactory, $scope) {
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
        busService.$emit('log.error', {
          'cause': reason,
          'msg': 'Hentning af tilgængelige delingsindeks fejlede.'
        });
      }
    );
    $scope.chosenIndexes = [];
    sharedChannelFactory.getSharingIndexes().then(
      function success(data) {
        $scope.chosenIndexes = data;
      },
      function error(reason) {
        busService.$emit('log.error', {
          'cause': reason,
          'msg': 'Hentning af valgte delingsindeks fejlede.'
        });
      }
    );

    $scope.save = function () {
      $scope.saving = true;
      sharedChannelFactory.saveSharingIndexes($scope.chosenIndexes).then(
        function success() {
          busService.$emit('log.info', {
            'cause': reason,
            'msg': 'Delingsindeks gemt',
            'timeout': 3000
          });
          $scope.saving = false;
        },
        function error(reason) {
          busService.$emit('log.error', {
            'cause': reason,
            'msg': 'Delingsindeks blev ikke gemt.'
          });
          $scope.saving = false;
        }
      );
    }
  }
]);