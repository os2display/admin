/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('PopupCreateGroup', [
  'busService', '$scope', '$timeout', 'close', '$controller',
  function (busService, $scope, $timeout, close, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    $scope.group = {
      title: ""
    };
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

      if (form.$invalid) {
        $scope.errors.push("Ugyldig input");

        return;
      }

      $scope.loading = true;

      $scope.createEntity('group', $scope.group).then(
        function success(group) {
          // Display message success.
          busService.$emit('log.info', {
            timeout: 5000,
            msg: 'Gruppen blev oprettet.'
          });

          close(group);
        },
        function error(err) {
          if (err.code === 409) {
            $scope.errors.push('Gruppen eksisterer allerede.');
          }
          else {
            $scope.errors.push('Kunne ikke oprette gruppen.');
          }
        }
      ).then(function () {
        $scope.loading = false;
      });
    };
  }
]);
