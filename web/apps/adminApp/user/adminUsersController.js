/**
 * @file
 * Controller for the admin users page.
 */

angular.module('adminApp').controller('AdminUsersController', ['busService', '$scope', '$timeout', 'ModalService', '$controller',
  function (busService, $scope, $timeout, ModalService, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', { $scope: $scope });

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
    var cleanupUsersListener = busService.$on('AdminUsersController.returnUsers', function (event, result) {
      $timeout(function () {
        if (result.error) {
          // Display message success.
          busService.$emit('log.error', {
            cause: result.error.code,
            msg: 'Brugere kunne ikke hentes.'
          });

          // Remove spinner.
          $scope.usersLoading = false;

          return;
        }

        $scope.users = [];

        for (var user in result) {
          if (result.hasOwnProperty(user)) {
            addUser(result[user]);
          }
        }

        $scope.usersLoading = false;
      });
    });

    // Emit event to get the user.
    busService.$emit('apiService.getEntities', {
      type: 'user',
      returnEvent: 'AdminUsersController.returnUsers'
    });

    // Show create user modal.
    $scope.createUser = function () {
      // Just provide a template url, a controller and call 'showModal'.
      ModalService.showModal({
        templateUrl: "apps/adminApp/users/popup-create-user.html",
        controller: "PopupCreateUser"
      }).then(function (modal) {
        modal.close.then(function (user) {
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
