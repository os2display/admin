/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('PopupCreateGroup', ['busService', '$scope', '$timeout', 'close', '$controller',
  function (busService, $scope, $timeout, close, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', { $scope: $scope });

    $scope.groupTitle = "";
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

      if (form.$invalid) {
        $scope.errors.push("Ugyldig input");

        return;
      }

      $scope.loading = true;

      busService.$emit('apiService.createEntity', {
        type: 'group',
        returnEvent: 'PopupCreateGroup.returnCreateGroup',
        data: {
          title: $scope.groupTitle
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
