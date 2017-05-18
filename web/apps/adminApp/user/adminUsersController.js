/**
 * @file
 * Controller for the admin users page.
 */

angular.module('adminApp').controller('AdminUsersController', [
  'busService', '$scope', '$timeout', 'ModalService', '$controller', '$filter',
  function (busService, $scope, $timeout, ModalService, $controller, $filter) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    // Check role.
    $scope.requireRole('ROLE_USER_ADMIN');

    // Get translation filter.
    var $translate = $filter('translate');

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

      if ($scope.baseCanRead(user)) {
        actions.push({
          url: '#/admin/user/' + user.id,
          title: $translate('user.action.view')
        });
      }
      if ($scope.baseCanUpdate(user)) {
        actions.push({
          url: '#/admin/user/' + user.id,
          title: $translate('user.action.edit')
        });
      }
      if ($scope.baseCanDelete(user)) {
        actions.push({
          click: $scope.deleteUser,
          entity: user,
          title: $translate('user.action.delete')
        });
      }

      $scope.users.push({
        id: user.id,
        url: '#/admin/user/' + user.id,
        title: user.displayName,
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
        $scope.baseRemoveElementFromList($scope.users, user, 'id');
      });
    }

    // Get the users.
    $scope.getEntities('user').then(
      function success(users) {
        $scope.users = [];

        for (var user in users) {
          if (users.hasOwnProperty(user)) {
            addUser(users[user]);
          }
        }
      },
      function error(err) {
        // Display message success.
        busService.$emit('log.error', {
          cause: err.code,
          msg: $translate('user.messages.users_not_found')
        });
      }
    ).then(function () {
      $scope.usersLoading = false;
    });

    /**
     * Show create user modal.
     */
    $scope.createUser = function () {
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
  }
]);
