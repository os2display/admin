/**
 * @file
 * Controller for the admin users page.
 */

angular.module('adminApp').controller('AdminUsersController', [
  'busService', '$scope', '$timeout', 'ModalService', '$controller',
  function (busService, $scope, $timeout, ModalService, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', {$scope: $scope});

    $scope.usersLoading = true;
    $scope.users = null;
    $scope.max = 50;

    /**
     * Add a user to the users array.
     *
     * @param user
     *   The user to add.
     */
    function addUser(user) {
      var actions = [];

      if ($scope.canRead(user)) {
        actions.push({
          url: '#/admin/user/' + user.id,
          title: 'Se bruger'
        });
      }
      if ($scope.canUpdate(user)) {
        actions.push({
          url: '#/admin/user/' + user.id,
          title: 'Rediger bruger'
        });
      }
      if ($scope.canDelete(user)) {
        actions.push({
          click: $scope.deleteUser,
          entity: user,
          title: 'Slet bruger'
        });
      }

      $scope.users.push({
        id: user.id,
        url: '#/admin/user/' + user.id,
        title: user.firstname ? user.firstname + (user.lastname ? " " + user.lastname : '') : user.username,
        actions: actions
      });
    }

    /**
     * Remove a user to the users array.
     *
     * @param user
     *   The user to remove.
     */
    function removeUser(user) {
      $timeout(function () {
        var findUser = $scope.users.findIndex(function (element, index, array) {
          return element.id === user.id;
        });

        if (findUser) {
          $scope.users.splice(findUser, 1);
        }
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
        templateUrl: "apps/adminApp/user/popup-create-user.html",
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
     * Show delete user modal.
     */
    $scope.deleteUser = function (user) {
      // Show modal.
      ModalService.showModal({
        templateUrl: "apps/adminApp/user/popup-delete-user.html",
        controller: "PopupDeleteUser",
        inputs: {
          user: user
        }
      }).then(function (modal) {
        modal.close.then(function (user) {
          if (user) {
            removeUser(user);
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
