/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('PopupDeleteGroup', [
  'busService', '$scope', '$timeout', 'close', '$controller', 'group', '$filter',
  function (busService, $scope, $timeout, close, $controller, group, $filter) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    // Get translation filter.
    var $translate = $filter('translate');

    $scope.group = group;
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

      $scope.loading = true;

      $scope.deleteEntity('group', $scope.group).then(
        function success() {
          // Display message success.
          busService.$emit('log.info', {
            timeout: 5000,
            msg: $translate('group.messages.group_deleted')
          });

          close($scope.group);
        },
        function error(err) {
          $scope.errors.push($translate('group.texts.error_could_not_delete_group'));
        }
      ).then(function () {
        $scope.loading = false;
      })
    };
  }
]);
