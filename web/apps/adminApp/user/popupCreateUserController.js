/**
 * @file
 * Controller for the popup: create user.
 */

angular.module('adminApp').controller('PopupCreateUser', [
  'busService', '$scope', '$timeout', 'close', '$controller', '$filter',
  function (busService, $scope, $timeout, close, $controller, $filter) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    // Get translation filter.
    var $translate = $filter('translate');

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
        $scope.errors.push($translate('user.texts.error_email_not_valid'));
        return;
      }

      $scope.loading = true;

      $scope.createEntity('user', $scope.user).then(
        function success(user) {
          // Display message success.
          busService.$emit('log.info', {
            timeout: 5000,
            msg: $translate('user.message.user_created')
          });

          close(user);
        },
        function error(err) {
          if (err.code === 400) {
            $scope.errors.push($translate('common.form.invalid'));
          }
          else if (err.code === 409) {
            $scope.errors.push($translate('user.message.user_not_create_conflict'));
          }
          else {
            $scope.errors.push($translate('user.message.user_not_create'));
          }
        }
      ).then(function () {
        $scope.loading = false;
      });
    };
  }
]);
