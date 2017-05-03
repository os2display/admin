/**
 * @file
 * Controller for the popup: create user.
 */

angular.module('adminApp').controller('PopupCreateUser', ['busService', '$scope', '$timeout', 'close',
  function (busService, $scope, $timeout, close) {
    'use strict';

    $scope.email = "";
    $scope.loading = false;

    $scope.closeModal = function() {
      //  Now close as normal, but give 500ms for bootstrap to animate
      close(null);
    };

    $scope.createUser = function () {
      if ($scope.loading) {
        return;
      }

      $scope.errors = [];

      if ($scope.userCreateForm.emailInput.$invalid) {
        $scope.errors.push('Ugyldig email.');
        return;
      }

      $scope.loading = true;

      busService.$emit('userService.createUser', {
        'email': $scope.email
      });
    };

    /**
     * returnCreateErrorUser listener.
     * @type {*}
     */
    var cleanupReturnCreateUserErrorListener = busService.$on('userService.returnCreateUserError', function (event, err) {
      $timeout(function () {
        $scope.loading = false;

        if (err.error.code === 409) {
          $scope.errors.push("Bruger eksisterer allerede.");
        }
      });
    });

    /**
     * returnCreateUser listener.
     * @type {*}
     */
    var cleanupReturnCreateUserListener = busService.$on('userService.returnCreateUser', function (event, user) {
      $timeout(function () {
        $scope.creatingUser = false;

        // Display message success.
        busService.$emit('log.info', {
          timeout: 5000,
          msg: 'Bruger blev oprettet. Der er sendt en mail med oprettelsesinformation til brugeren.'
        });

        close(user);
      });
    });

    $scope.$on('$destroy', function () {
      cleanupReturnCreateUserListener();
      cleanupReturnCreateUserErrorListener();
    });
  }
]);
