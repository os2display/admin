/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('PopupCreateGroup', [
  'busService', '$scope', '$timeout', 'close', '$controller', '$filter',
  function (busService, $scope, $timeout, close, $controller, $filter) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    // Get translation filter.
    var $translate = $filter('translate');

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
        $scope.errors.push($translate('group.messages.form_invalid'));

        return;
      }

      $scope.loading = true;

      $scope.createEntity('group', $scope.group).then(
        function success(group) {
          // Display message success.
          busService.$emit('log.info', {
            timeout: 5000,
            msg: $translate('group.messages.group_created')
          });

          close(group);
        },
        function error(err) {
          if (err.code === 409) {
            $scope.errors.push($translate('group.messages.group_already_exists'));
          }
          else {
            $scope.errors.push($translate('group.messages.could_not_create_group'));
          }
        }
      ).then(function () {
        $scope.loading = false;
      });
    };
  }
]);
