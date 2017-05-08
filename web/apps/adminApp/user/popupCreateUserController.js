/**
 * @file
 * Controller for the popup: create user.
 */

angular.module('adminApp').controller('PopupCreateUser', [
  'busService', '$scope', '$timeout', 'close', '$controller',
  function (busService, $scope, $timeout, close, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    $scope.user = {
      email: ''
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

      if (form.emailInput.$invalid) {
        $scope.errors.push('Ugyldig email.');
        return;
      }

      $scope.loading = true;

      $scope.createEntity('user', $scope.user).then(
        function success(user) {
          // Display message success.
          busService.$emit('log.info', {
            timeout: 5000,
            msg: 'Brugeren blev oprettet.'
          });

          close(user);
        },
        function error(err) {
          if (err.code === 400) {
            $scope.errors.push("Ugyldigt input.");
          }
          else if (err.code === 409) {
            $scope.errors.push("Brugeren eksisterer allerede.");
          }
          else {
            $scope.errors.push("Brugeren kunne ikke oprettes.");
          }
        }
      ).then(function () {
        $scope.loading = false;
      });
    };
  }
]);
