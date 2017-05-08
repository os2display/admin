/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('PopupDeleteGroup', ['busService', '$scope', '$timeout', 'close', '$controller', 'group',
  function (busService, $scope, $timeout, close, $controller, group) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', { $scope: $scope });

    $scope.loading = false;
    $scope.errors = [];
    $scope.group = group;
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

      $scope.loading = true;

      busService.$emit('apiService.deleteEntity', {
        type: 'group',
        returnEvent: 'PopupCreateGroup.returnDeleteGroup',
        data: $scope.group
      });
    };

    /**
     * returnCreateGroup listener.
     * @type {*}
     */
    var cleanupReturnDeleteGroupListener = busService.$on('PopupCreateGroup.returnDeleteGroup', function (event, result) {
      $timeout(function () {
        $scope.loading = false;

        if (result && result.error) {
          console.log(result.error);

          return;
        }

        $scope.creatingGroup = false;

        // Display message success.
        busService.$emit('log.info', {
          timeout: 5000,
          msg: 'Gruppen blev slettet.'
        });

        close(group);
      });
    });

    /**
     * on destroy.
     *
     * Clean up listeners.
     */
    $scope.$on('$destroy', function () {
      cleanupReturnDeleteGroupListener();
    });
  }
]);
