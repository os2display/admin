/**
 * @file
 * Controller for the admin groups page.
 */

angular.module('adminApp').controller('AdminGroupsController', ['busService', '$scope', '$timeout',
  function (busService, $scope, $timeout) {
    'use strict';

    $scope.groups = null;

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
      });
    });

    // Request users and groups.
    busService.$emit('groupService.getGroups', {});

    $scope.addGroup = function () {
      console.log('addGroup');
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
