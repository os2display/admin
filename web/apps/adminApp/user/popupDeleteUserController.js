/**
 * @file
 * Controller for the popup: create user.
 */

angular.module('adminApp').controller('PopupDeleteUser', [
  'busService', '$scope', '$timeout', 'close', '$controller', 'user',
  function (busService, $scope, $timeout, close, $controller, user) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    $scope.user = user;
    $scope.loading = false;
    $scope.errors = [];

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

      $scope.deleteEntity('user', $scope.user).then(
        function success() {
          // Display message success.
          busService.$emit('log.info', {
            timeout: 5000,
            msg: 'Brugeren blev slettet.'
          });

          close($scope.user);
        },
        function error(err) {
          // Display message success.
          busService.$emit('log.error', {
            timeout: 5000,
            msg: 'Brugeren kunne ikke slettes.'
          });
        }
      ).then(function () {
        $scope.loading = false;
      });
    }
  }
]);
