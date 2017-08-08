/**
 * @file
 * Controller for the popup: create user.
 */

angular.module('adminApp').controller('PopupDeleteUser', [
  'busService', '$scope', '$timeout', 'close', '$controller', 'user', '$filter',
  function (busService, $scope, $timeout, close, $controller, user, $filter) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    // Get translation filter.
    var $translate = $filter('translate');

    $scope.user = user;
    $scope.loading = false;

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

      $scope.loading = true;

      $scope.deleteEntity('user', $scope.user).then(
        function success() {
          // Display message success.
          busService.$emit('log.info', {
            timeout: 5000,
            msg: $translate('user.messages.user_deleted')
          });

          close($scope.user);
        },
        function error(err) {
          // Display message success.
          busService.$emit('log.error', {
            cause: err.code,
            timeout: 5000,
            msg: $translate('user.messages.user_not_deleted')
          });
        }
      ).then(function () {
        $scope.loading = false;
      });
    }
  }
]);
