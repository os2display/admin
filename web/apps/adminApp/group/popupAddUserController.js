/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('PopupAddUser', [
  'busService', '$scope', '$timeout', 'close', '$controller', 'group', 'addedUser',
  function (busService, $scope, $timeout, close, $controller, group, addedUser) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    $scope.loading = false;
    $scope.errors = [];
    $scope.forms = {};
    $scope.users = [];

    /**
     * Add user to group.
     *
     * @param user
     */
    $scope.addUser = function (user) {
      $scope.baseApiRequest('post', '/api/user/' + user.id + '/group/' + group.id, {
        roles: ['ROLE_USER']
      }).then(
        function success(res) {
          addedUser(user);
        },
        function error(err) {
          console.error(err);
        }
      );
    };

    /**
     * Add user to display list.
     *
     * @param user
     */
    function addUserToList(user) {
      $scope.users.push({
        id: user.id,
        title: user.firstname ? user.firstname + (user.lastname ? " " + user.lastname : '') : user.username,
        entity: user,
        click: $scope.addUser
      });
    }

    // Get users.
    $scope.getEntities('user').then(
      function success(users) {
        for (var user in users) {
          addUserToList(users[user]);
        }
      },
      function error(err) {
        console.error(err);
      }
    ).then(function() {
      $scope.loading = false;
    });

    /**
     * Close the modal.
     */
    $scope.closeModal = function () {
      close(null);
    };
  }
]);
