/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('PopupDeleteGroup', [
  'busService', '$scope', '$timeout', 'close', '$controller', 'group',
  function (busService, $scope, $timeout, close, $controller, group) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    $scope.group = group;
    $scope.loading = false;
    $scope.errors = [];
    $scope.forms = {};

    /**
     * Close the modal.
     */
    $scope.closeModal = function () {
      close(null);
    };

    /**
     * Submit form.
     *
     * @param form
     */
    $scope.submitForm = function (form) {
      if ($scope.loading) {
        return;
      }

      $scope.errors = [];

      $scope.loading = true;

      $scope.deleteEntity('group', $scope.group).then(
        function success() {
          // Display message success.
          busService.$emit('log.info', {
            timeout: 5000,
            msg: 'Gruppen blev slettet'
          });

          close($scope.group);
        },
        function error(err) {
          $scope.errors.push(err.message);
        }
      ).then(function () {
        $scope.loading = false;
      })
    };
  }
]);
