/**
 * @file
 * Controller for the popup: create user.
 */

angular.module('adminApp').controller('PopupDeleteUser', ['busService', '$scope', '$timeout', 'close', '$controller', 'user',
  function (busService, $scope, $timeout, close, $controller, user) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', { $scope: $scope });

    $scope.loading = false;
    $scope.errors = [];
    $scope.user = user;

    /**
     * Close the modal.
     */
    $scope.closeModal = function() {
      close(null);
    };

    /**
     * Submit form.
     *
     * @param form
     */
    $scope.submitForm = function(form){
      if ($scope.loading) {
        return;
      }

      $scope.errors = [];

      $scope.loading = true;

      busService.$emit('apiService.deleteEntity', {
        type: 'user',
        returnEvent: 'PopupCreateUser.returnDeleteUser',
        data: user
      });
    };

    /**
     * returnCreateUser listener.
     * @type {*}
     */
    var cleanupReturnDeleteUserListener = busService.$on('PopupCreateUser.returnDeleteUser', function (event, result) {
      $timeout(function () {
        $scope.loading = false;

        if (result && result.error) {
          return;
        }

        $scope.creatingUser = false;

        // Display message success.
        busService.$emit('log.info', {
          timeout: 5000,
          msg: 'Brugeren blev slettet.'
        });

        close(user);
      });
    });

    /**
     * on destroy.
     *
     * Clean up listeners.
     */
    $scope.$on('$destroy', function () {
      cleanupReturnDeleteUserListener();
    });
  }
]);
