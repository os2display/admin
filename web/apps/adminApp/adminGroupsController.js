/**
 * @file
 * Controller for the admin groups page.
 */

angular.module('adminApp').controller('AdminGroupsController', ['busService', '$scope', 'userService', 'groupService',
  function (busService, $scope, userService, groupService) {
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

    // Get all groups.
    groupService.getGroups().then(
      function success(data) {
        $scope.groups = [];

        for (var group in data) {
          if (data.hasOwnProperty(group)) {
            addGroup(data[group]);
          }
        }
      }
    );

    $scope.addGroup = function () {
      console.log('addGroup');

      // Create popup.
      groupService.createGroup('Super duper').then(
        function success(data) {
          addGroup(data);
        },
        function error(err) {
          console.log(err);
        }
      );
    };
  }
]);
