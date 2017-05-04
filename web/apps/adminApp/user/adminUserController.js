/**
 * @file
 * Controller for the admin user page.
 */

angular.module('adminApp').controller('AdminUserController', ['busService', '$scope', '$timeout', 'ModalService', '$routeParams', '$location', '$controller',
  function (busService, $scope, $timeout, ModalService, $routeParams, $location, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', { $scope: $scope });

    $scope.user = null;
    $scope.loading = true;

    /**
     * returnUsers listener.
     * @type {*}
     */
    var cleanupUserListener = busService.$on('AdminUserController.returnUser', function (event, result) {
      $timeout(function () {
        if (result.error) {
          busService.$emit('log.error', {
            timeout: 5000,
            cause: result.error.code,
            msg: 'Bruger kan ikke findes.'
          });

          // Redirect to dashboard.
          $location.path('/admin');

          return;
        }

        // Update the user with data from database.
        $scope.user = result;

        // Remove spinner.
        $scope.loading = false;
      });
    });

    /**
     * returnUpdateUser listener.
     * @type {*}
     */
    var cleanupUpdateUserListener = busService.$on('AdminUserController.returnUpdateUser', function (event, result) {
      $timeout(function () {
        if (result.error) {
          // Display message success.
          busService.$emit('log.error', {
            cause: result.error.code,
            msg: 'Bruger kunne ikke opdateres.'
          });

          // Remove spinner.
          $scope.loading = false;

          return;
        }

        // Update the user with data from database.
        $scope.user = result;

        // Remove spinner.
        $scope.loading = false;

        // Display message success.
        busService.$emit('log.info', {
          timeout: 3000,
          msg: 'Bruger opdateret.'
        });
      });
    });

    /**
     * returnCurrentUser listener.
     * @type {*}
     */
    var cleanupCurrentUserListener = busService.$on('userService.returnCurrentUser', function (event, result) {
      $timeout(function () {
        // Update the user with data from database.
        $scope.user = result;

        // Remove spinner.
        $scope.loading = false;
      });
    });

    // If id set, request that user, else request current user.
    if ($routeParams.id) {
      // Emit event to get the user.
      busService.$emit('apiService.getEntity', {
        type: 'user',
        returnEvent: 'AdminUserController.returnUser',
        data: {
          id: $routeParams.id
        }
      });
    }
    else {
      // Emit event to get the user.
      busService.$emit('userService.getCurrentUser', {});
    }

    /**
     * Save user.
     */
    $scope.saveUser = function () {
      $scope.loading = true;

      // Emit event to update user.
      busService.$emit('apiService.updateEntity', {
        type: 'user',
        returnEvent: 'AdminUserController.returnUpdateUser',
        data: $scope.user
      });
    };

    /**
     * on destroy.
     *
     * Clean up listeners.
     */
    $scope.$on('$destroy', function destroy() {
      cleanupUserListener();
      cleanupUpdateUserListener();
      cleanupCurrentUserListener();
    });
  }
]);
