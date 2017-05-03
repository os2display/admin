/**
 * @file
 * Controller for the admin groups page.
 */

angular.module('adminApp').controller('AdminGroupsController', ['busService', '$scope', '$timeout', 'ModalService',
  function (busService, $scope, $timeout, ModalService) {
    'use strict';

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
        url: '/admin/group/' + group.id,
        title: group.title
      });
    }

    /**
     * returnGroups listener.
     * @type {*}
     */
    var cleanupGetGroupsListener = busService.$on('groupService.returnGroups', function (event, groups) {
      $scope.groups = [];

      $timeout(function () {
        for (var group in groups) {
          if (groups.hasOwnProperty(group)) {
            addGroup(groups[group]);
          }
        }

        $scope.groupsLoading = false;
      });
    });

    // Request users and groups.
    busService.$emit('groupService.getGroups', {});

    // Show create group modal.
    $scope.createGroup = function () {
      // Just provide a template url, a controller and call 'showModal'.
      ModalService.showModal({
        templateUrl: "apps/adminApp/popup-create-group.html",
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
