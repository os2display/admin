/**
 * @file
 * Controller for the admin groups page.
 */

angular.module('adminApp').controller('AdminGroupsController', ['busService', '$scope', '$timeout', 'ModalService', '$controller',
  function (busService, $scope, $timeout, ModalService, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', { $scope: $scope });

    $scope.groupsLoading = true;
    $scope.groups = null;
    $scope.max = 50;

    /**
     * Add a group to the groups array.
     * @param group
     */
    function addGroup(group) {
      $scope.groups.push({
        id: group.id,
        url: '#/admin/group/' + group.id,
        title: group.title
      });
    }

    /**
     * returnGroups listener.
     * @type {*}
     */
    var cleanupGetGroupsListener = busService.$on('AdminGroupsController.returnGroups', function (event, result) {
      $timeout(function () {
        if (result.error) {
          // Display message success.
          busService.$emit('log.error', {
            cause: result.error.code,
            msg: 'Grupper kunne ikke hentes.'
          });

          // Remove spinner.
          $scope.groupsLoading = false;

          return;
        }

        $scope.groups = [];

        for (var group in result) {
          if (result.hasOwnProperty(group)) {
            addGroup(result[group]);
          }
        }

        $scope.groupsLoading = false;
      });
    });

    // Get groups.
    busService.$emit('apiService.getEntities', {
      type: 'group',
      returnEvent: 'AdminGroupsController.returnGroups'
    });

    // Show create group modal.
    $scope.createGroup = function () {
      // Just provide a template url, a controller and call 'showModal'.
      ModalService.showModal({
        templateUrl: "apps/adminApp/groups/popup-create-group.html",
        controller: "PopupCreateGroup"
      }).then(function(modal) {
        modal.close.then(function(group) {
          if (group) {
            addGroup(group);
          }
        });
      });
    };

    /**
     * on destroy.
     *
     * Clean up listeners.
     */
    $scope.$on('$destroy', function destroy() {
      cleanupGetGroupsListener();
    });
  }
]);
