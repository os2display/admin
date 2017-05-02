/**
 * @file
 * Controller for the admin users page.
 */

angular.module('adminApp').controller('AdminUsersController', ['busService', '$scope', '$timeout',
  function (busService, $scope, $timeout) {
    'use strict';

    $scope.users = null;

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

    busService.$emit('userService.getUsers', {});

    $scope.addUser = function () {
      console.log('addUser');
    };

    /**
     * on destroy.
     *
     * Clean up listeners.
     */
    $scope.$on('$destroy', function destroy() {
      cleanupUsersListener();
    });
  }
]);
