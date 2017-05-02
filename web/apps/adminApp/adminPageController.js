/**
 * @file
 * Controller for the channel overview time-line.
 */
angular.module('adminApp').controller('AdminPageController', ['busService', '$scope', 'userService', 'groupService',
  function (busService, $scope, userService, groupService) {
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
    function addUser(user) {
      $scope.users.push({
        id: user.id,
        url: '/admin/user/' + user.id,
        title: user.firstname ? user.firstname + (user.lastname ? " " + user.lastname : '') : user.username
      });
    }

    // Get all users.
    userService.getUsers().then(
      function success(data) {
        $scope.users = [];

        for (var user in data) {
          if (data.hasOwnProperty(user)) {
            addUser(data[user]);
          }
        }
      }
    );

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

    $scope.addUser = function () {
      console.log('add user');
    };

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
