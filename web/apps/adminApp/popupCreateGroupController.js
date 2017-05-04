/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('PopupCreateGroup', ['busService', '$scope', '$timeout', 'close',
  function (busService, $scope, $timeout, close) {
    'use strict';

    $scope.group = "";
    $scope.loading = false;
    $scope.errors = [];

    /**
     * Close the modal.
     */
    $scope.closeModal = function() {
      close(null);
    };

    /**
     * Create group.
     */
    $scope.createGroup = function () {
      if ($scope.loading) {
        return;
      }

      $scope.errors = [];

      if ($scope.groupCreateForm.textInput.$invalid) {
        return;
      }

      $scope.loading = true;

      busService.$emit('apiService.createEntity', {
        type: 'group',
        returnEvent: 'PopupCreateGroup.returnCreateGroup',
        data: {
          title: $scope.group
        }
      });
    };

    /**
     * returnCreateGroup listener.
     * @type {*}
     */
    var cleanupReturnCreateGroupListener = busService.$on('PopupCreateGroup.returnCreateGroup', function (event, result) {
      $timeout(function () {
        $scope.loading = false;

        if (result.error) {
          if (result.error.code === 409) {
            $scope.errors.push("Gruppen eksisterer allerede.");
          }

          return;
        }

        $scope.creatingGroup = false;

        // Display message success.
        busService.$emit('log.info', {
          timeout: 5000,
          msg: 'Gruppen blev oprettet.'
        });

        close(result);
      });
    });

    $scope.$on('$destroy', function () {
      cleanupReturnCreateGroupListener();
    });
  }
]);
