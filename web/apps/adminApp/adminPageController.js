/**
 * @file
 * Controller for the channel overview time-line.
 */
angular.module('adminApp').controller('AdminPageController', [
  'busService', '$scope', 'groupService', '$timeout',
  function (busService, $scope, groupService, $timeout) {
    'use strict';

    $scope.users = null;
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
     * Add a user to the users array.
     * @param user
     */
    function addUser(user) {
      $scope.users.push({
        id: user.id,
        url: '/admin/user/' + user.id,
        title: user.firstname ? user.firstname + (user.lastname ? " " + user.lastname : '') : user.username
      });
    }

    /**
     * returnUsers listener.
     * @type {*}
     */
    var cleanupUsersListener = busService.$on('userService.returnUsers', function (event, users) {
      $scope.users = [];

      $timeout(function () {
        for (var user in users) {
          if (users.hasOwnProperty(user)) {
            addUser(users[user]);
          }
        }
      });
    });

    /**
     * returnGroups listener.
     * @type {*}
     */
    var cleanupGroupsListener = busService.$on('groupService.returnGroups', function (event, groups) {
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
    busService.$emit('userService.getUsers', {});

    /**
     * Add a new user.
     */
    $scope.addUser = function () {
      console.log('add user');
    };

    /**
     * Add a new group.
     */
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

    /**
     * on destroy.
     *
     * Clean up listeners.
     */
    $scope.$on('$destroy', function destroy() {
      cleanupUsersListener();
      cleanupGroupsListener();
    });
  }
]);
