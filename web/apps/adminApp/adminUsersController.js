/**
 * @file
 * Controller for the admin users page.
 */

angular.module('adminApp').controller('AdminUsersController', ['busService', '$scope', '$timeout', 'ModalService',
  function (busService, $scope, $timeout, ModalService) {
    'use strict';

    $scope.usersLoading = true;
    $scope.users = null;
    $scope.max = 50;

    /**
     * Add a user to the users array.
     * @param user
     */
    function addUser(user) {
      $scope.users.push({
        id: user.id,
        url: '#/admin/user/' + user.id,
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

        $scope.usersLoading = false;
      });
    });

    busService.$emit('userService.getUsers', {});

    // Show create user modal.
    $scope.createUser = function () {
      // Just provide a template url, a controller and call 'showModal'.
      ModalService.showModal({
        templateUrl: "apps/adminApp/popup-create-user.html",
        controller: "PopupCreateUser"
      }).then(function(modal) {
        modal.close.then(function(user) {
          if (user) {
            addUser(user);
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
      cleanupUsersListener();
    });
  }
]);
