/**
 * @file
 * Controller for the admin user page.
 */

angular.module('adminApp').controller('AdminUserController', ['busService', '$scope', '$timeout', 'ModalService', '$routeParams', '$location', '$controller',
  function (busService, $scope, $timeout, ModalService, $routeParams, $location, $controller) {
    'use strict';

    // Extend BaseApiController.
    $controller('BaseApiController', { $scope: $scope });

    $scope.user = null;
    $scope.loading = true;
    $scope.forms = {};

    // If id set, request that user, else use baseCurrentUser (from BaseController).
    if ($routeParams.id) {
      $scope.getEntity('user', { id: $routeParams.id }).then(
        function success(user) {
          $scope.user = user;
        },
        function error(err) {
          busService.$emit('log.error', {
            timeout: 5000,
            cause: err.code,
            msg: 'Brugeren kan ikke findes.'
          });

          // Redirect to dashboard.
          $location.path('/admin');
        }
      ).then(function () {
        $scope.loading = false;
      });
    }
    else {
      // Get user from BaseController.
      $scope.user = $scope.baseCurrentUser;

      // Remove spinner.
      $scope.loading = false;
    }

    /**
     * Submit form.
     */
    $scope.submitForm = function submitForm(form) {
      if ($scope.loading) {
        return;
      }

      if (form.$invalid) {
        busService.$emit('log.error', {
          timeout: 5000,
          cause: err.code,
          msg: 'Ugyldigt input.'
        });

        return;
      }

      $scope.loading = true;

      $scope.updateEntity('user', $scope.user).then(
        function success(user) {
          $scope.user = user;

          // Display message success.
          busService.$emit('log.info', {
            timeout: 3000,
            msg: 'Bruger opdateret.'
          });
        },
        function error(err) {
          // Display message success.
          busService.$emit('log.error', {
            cause: result.error.code,
            msg: 'Bruger kunne ikke opdateres.'
          });
        }
      ).then(function () {
        $scope.loading = false;
      });
    };
  }
]);
