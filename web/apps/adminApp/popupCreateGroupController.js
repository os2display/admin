/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('PopupCreateGroup', ['busService', '$scope', '$timeout', 'close',
  function (busService, $scope, $timeout, close) {
    'use strict';

    $scope.group = "";
    $scope.loading = false;

    $scope.closeModal = function() {
      //  Now close as normal, but give 500ms for bootstrap to animate
      close(null);
    };

    $scope.createGroup = function () {
      if ($scope.loading) {
        return;
      }

      $scope.errors = [];

      if ($scope.groupCreateForm.textInput.$invalid) {
        return;
      }

      $scope.loading = true;

      busService.$emit('groupService.createGroup', {
        'title': $scope.group
      });
    };

    /**
     * returnCreateErrorGroup listener.
     * @type {*}
     */
    var cleanupReturnCreateGroupErrorListener = busService.$on('groupService.returnCreateGroupError', function (event, err) {
      $timeout(function () {
        $scope.loading = false;

        if (err.error.code === 409) {
          $scope.errors.push("Gruppen eksisterer allerede.");
        }
      });
    });

    /**
     * returnCreateGroup listener.
     * @type {*}
     */
    var cleanupReturnCreateGroupListener = busService.$on('groupService.returnCreateGroup', function (event, group) {
      $timeout(function () {
        $scope.creatingGroup = false;

        // Display message success.
        busService.$emit('log.info', {
          timeout: 5000,
          msg: 'Gruppen blev oprettet.'
        });

        close(group);
      });
    });

    $scope.$on('$destroy', function () {
      cleanupReturnCreateGroupListener();
      cleanupReturnCreateGroupErrorListener();
    });
  }
]);
