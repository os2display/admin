/**
 * @file
 * Controller for the admin groups page.
 */

angular.module('adminApp').controller('AdminGroupController', [
  'busService', '$scope', '$timeout', 'ModalService', '$routeParams', '$location', '$controller',
  function (busService, $scope, $timeout, ModalService, $routeParams, $location, $controller) {
    'use strict';

    // Extend
    $controller('BaseController', { $scope: $scope });

    $scope.group = null;
    $scope.loading = true;

    /**
     * returnGroups listener.
     * @type {*}
     */
    var cleanupGroupListener = busService.$on('AdminGroupController.returnGroup', function (event, result) {
      $timeout(function () {
        if (result.error) {
          busService.$emit('log.error', {
            timeout: 5000,
            cause: result.error.code,
            msg: 'Gruppe kan ikke findes.'
          });

          // Redirect to dashboard.
          $location.path('/admin');

          return;
        }

        // Update the group with data from database.
        $scope.group = result;

        // Remove spinner.
        $scope.loading = false;
      });
    });

    /**
     * returnUpdateGroup listener.
     * @type {*}
     */
    var cleanupUpdateGroupListener = busService.$on('AdminGroupController.returnUpdateGroup', function (event, result) {
      $timeout(function () {
        if (result.error) {
          // Display message success.
          busService.$emit('log.error', {
            cause: result.error.code,
            msg: 'Gruppe kunne ikke opdateres.'
          });

          return;
        }

        // Update the group with data from database.
        $scope.group = result;

        // Remove spinner.
        $scope.loading = false;

        // Display message success.
        busService.$emit('log.info', {
          timeout: 3000,
          msg: 'Gruppe opdateret.'
        });
      });
    });

    // Emit event to get the group.
    busService.$emit('apiService.getEntity', {
      type: 'group',
      returnEvent: 'AdminGroupController.returnGroup',
      data: {
        id: $routeParams.id
      }
    });

    /**
     * Edit group.
     */
    $scope.editGroup = function () {
      $scope.loading = true;

      // Emit event to update group.
      busService.$emit('apiService.updateEntity', {
        type: 'group',
        returnEvent: 'AdminGroupController.returnUpdateGroup',
        data: $scope.group
      });
    };

    /**
     * on destroy.
     *
     * Clean up listeners.
     */
    $scope.$on('$destroy', function destroy() {
      cleanupGroupListener();
      cleanupUpdateGroupListener();
    });
  }
]);
