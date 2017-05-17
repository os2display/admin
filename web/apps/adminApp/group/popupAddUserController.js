/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('PopupAddUser', [
  'busService', '$scope', '$timeout', 'close', '$controller', 'group', 'groupUsers', 'addedUserCallback', '$filter', 'userService',
  function (busService, $scope, $timeout, close, $controller, group, groupUsers, addedUserCallback, $filter, userService) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});

    // Get translation filter.
    var $translate = $filter('translate');

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
      $scope.errors = [];

      $scope.baseApiRequest('post', '/api/user/' + user.id + '/group/' + group.id, {
        roles: ['ROLE_GROUP_ROLE_USER']
      }).then(
        function success(res) {
          if (res.user.id === userService.getCurrentUser().id) {
            busService.$emit('log.info', {
              timeout: 10000,
              msg: $translate('user.messages.current_user_updated')
            });
          }

          addedUserCallback(user);

          $scope.baseRemoveElementFromList($scope.users, user, 'id');
        },
        function error(err) {
          $scope.errors.push($translate('group.texts.error_adding_user'));
        }
      );
    };

    /**
     * Add user to display list.
     *
     * @param user
     */
    function addUserToList(user) {
      var f = groupUsers.find(function (element) {
        return element.id === user.id;
      });

      if (!f) {
        $scope.users.push({
          id: user.id,
          title: user.displayName,
          entity: user,
          click: $scope.addUser
        });
      }
    }

    // Get users.
    $scope.users = [];
    $scope.getEntities('user').then(
      function success(users) {
        for (var user in users) {
          addUserToList(users[user]);
        }
      },
      function error(err) {
        busService.$emit('log.error', {
          timeout: 5000,
          cause: err.code,
          msg: $translate('group.messages.unable_to_load_users')
        });

        // Redirect to dashboard.
        $location.path('/admin');
      }
    ).then(function () {
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
