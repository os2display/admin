/**
 * @file
 * Controller for the popup: create user.
 */

angular.module('adminApp').controller('PopupCreateUser', ['busService', '$scope', '$timeout', 'close', '$controller',
  function (busService, $scope, $timeout, close, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', { $scope: $scope });

    $scope.userEmail = "";
    $scope.loading = false;
    $scope.errors = [];
    $scope.forms = {};

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

      if (form.emailInput.$invalid) {
        $scope.errors.push('Ugyldig email.');
        return;
      }

      $scope.loading = true;

      busService.$emit('apiService.createEntity', {
        type: 'user',
        returnEvent: 'PopupCreateUser.returnCreateUser',
        data: {
          email: this.userEmail
        }
      });
    };

    /**
     * returnCreateUser listener.
     * @type {*}
     */
    var cleanupReturnCreateUserListener = busService.$on('PopupCreateUser.returnCreateUser', function (event, result) {
      $timeout(function () {
        $scope.loading = false;

        if (result.error) {
          // @TODO: Better way of handling errors. Message should be created (and translated) in Symfony.
          if (result.error.code === 400) {
            $scope.errors.push("Ugyldigt input.");
          }
          if (result.error.code === 409) {
            $scope.errors.push("Brugeren eksisterer allerede.");
          }

          return;
        }

        $scope.creatingUser = false;

        // Display message success.
        busService.$emit('log.info', {
          timeout: 5000,
          msg: 'Brugeren blev oprettet.'
        });

        close(result);
      });
    });

    $scope.$on('$destroy', function () {
      cleanupReturnCreateUserListener();
    });
  }
]);
