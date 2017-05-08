/**
 * @file
 * Controller for the admin groups page.
 */

angular.module('adminApp').controller('AdminGroupController', [
  'busService', '$scope', '$timeout', 'ModalService', '$routeParams', '$location', '$controller',
  function (busService, $scope, $timeout, ModalService, $routeParams, $location, $controller) {
    'use strict';

    // Extend
    $controller('BaseApiController', {$scope: $scope});

    $scope.group = null;
    $scope.loading = true;

    // Get the group.
    $scope.getEntity('group', {id: $routeParams.id}).then(
      function success(result) {
        // Update the group with data from database.
        $scope.group = result;

        // Remove spinner.
        $scope.loading = false;
      },
      function error(err) {
        busService.$emit('log.error', {
          timeout: 5000,
          cause: err.code,
          msg: 'Gruppe kan ikke findes.'
        });

        // Redirect to dashboard.
        $location.path('/admin');
      }
    );

    /**
     * Submit form.
     */
    $scope.submitForm = function (form) {
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

      // Emit event to update group.
      $scope.updateEntity('group', $scope.group).then(
        function success(result) {
          // Update the group with data from database.
          $scope.group = result;

          // Remove spinner.
          $scope.loading = false;

          // Display message success.
          busService.$emit('log.info', {
            timeout: 3000,
            msg: 'Gruppe opdateret.'
          });
        },
        function error(err) {
          // Display message success.
          busService.$emit('log.error', {
            cause: err.code,
            msg: 'Gruppe kunne ikke opdateres.'
          });
        }
      );
    };
  }
]);
