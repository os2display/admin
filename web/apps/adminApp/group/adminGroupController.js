/**
 * @file
 * Controller for the admin groups page.
 */

angular.module('adminApp').controller('AdminGroupController', [
  'busService', '$scope', '$timeout', 'ModalService', '$routeParams', '$location', '$controller', '$filter', 'userService',
  function (busService, $scope, $timeout, ModalService, $routeParams, $location, $controller, $filter, userService) {
    'use strict';

    // Extend
    $controller('BaseApiController', {$scope: $scope});

    // Check role.
    $scope.requireRole('ROLE_GROUP_ADMIN');

    // Get translation filter.
    var $translate = $filter('translate');

    $scope.group = null;
    $scope.loading = true;
    $scope.users = null;

    var groupRoles = null;

    /**
     * Set role for user.
     *
     * @param entity
     */
    $scope.setRoleToUser = function(entity) {
      $scope.baseApiRequest('put', '/api/user/' + entity.user.id + '/group/' + $scope.group.id, {
        roles: entity.roles
      }).then(
        function success(userGroup) {
          busService.$emit('log.info', {
            timeout: 2000,
            msg: $translate('group.messages.user_role_set')
          });

          entity.user.roles = userGroup.roles;

          // Remove an re-add role.
          $scope.baseRemoveElementFromList($scope.users, entity.user, 'id');
          addToUsers(entity.user);
        },
        function error(err) {
          busService.$emit('log.error', {
            cause: err.code,
            msg: $translate('group.messages.user_role_not_set')
          });
        }
      )
    };

    /**
     * Remove user from group.
     */
    $scope.removeUserFromGroup = function (user) {
      $scope.baseApiRequest('delete', '/api/user/' + user.id + '/group/' + $scope.group.id).then(
        function success() {
          $timeout(function () {
            busService.$emit('log.info', {
              timeout: 2000,
              msg: $translate('group.messages.user_removed_from_group')
            });

            if (user.id === userService.getCurrentUser().id) {
              busService.$emit('log.info', {
                timeout: 10000,
                msg: $translate('user.messages.current_user_updated')
              });
            }

            $scope.baseRemoveElementFromList($scope.users, user, 'id');
          });
        },
        function error(err) {
          busService.$emit('log.error', {
            timeout: 5000,
            cause: err.code,
            msg: $translate('group.messages.user_not_removed_from_group')
          });
        }
      )
    };

    /**
     * Add user to users list.
     *
     * @param user
     */
    function addToUsers(user) {
      var actions = [];

      // @TODO: Use roles from user object instead of extra load.

      $scope.baseApiRequest('get', '/api/user/' + user.id + '/group/' + $scope.group.id).then(
        function success(group) {
          if ($scope.baseCanUpdate($scope.group)) {
            for (var roleId in groupRoles) {
              if (group.roles.indexOf(roleId) === -1) {
                var roleName = groupRoles[roleId];

                actions.push({
                  title: $translate('group.action.set_group_role', { 'roleName': roleName }),
                  click: $scope.setRoleToUser,
                  entity: {
                    user: user,
                    roles: [ roleId ]
                  }
                });
              }
            }

            actions.push({
              title: $translate('group.action.remove_user'),
              click: $scope.removeUserFromGroup,
              entity: user
            });
          }

          var text = "";

          for (var role in group.roles) {
            role = group.roles[role];

            text = text + "(" + (groupRoles[role] ? groupRoles[role] : role)  + ") ";
          }

          $scope.users.push({
            id: user.id,
            url: '#/admin/user/' + user.id,
            title: user.displayName,
            actions: actions,
            text: text
          });
        },
        function error(err) {
          console.error(err);
        }
      );
    }

    // Load user roles.
    $scope.baseApiRequest('get', '/api/group/roles').then(
      function (roles) {
        groupRoles = roles;
      }
    ).then(function () {
      // Get the group.
      $scope.getEntity('group', {id: $routeParams.id}).then(
        function success(group) {
          // Update the group with data from database.
          $scope.group = group;

          $scope.users = [];

          for (var user in $scope.group.users) {
            addToUsers($scope.group.users[user]);
          }

          // Remove spinner.
          $scope.loading = false;
        },
        function error(err) {
          busService.$emit('log.error', {
            timeout: 5000,
            cause: err.code,
            msg: $translate('group.messages.group_not_found')
          });

          // Redirect to dashboard.
          $location.path('/admin');
        }
      );
    });

    /**
     * Show add user modal.
     */
    $scope.showAddUserModal = function showAddUserModal() {
      ModalService.showModal({
        templateUrl: "apps/adminApp/group/popup-add-user.html",
        controller: "PopupAddUser",
        inputs: {
          group: $scope.group,
          groupUsers: $scope.users,
          addedUserCallback: addToUsers
        }
      }).then(function (modal) {
        modal.close.then(function () {});
      });
    };

    /**
     * Submit form.
     */
    $scope.submitForm = function (form) {
      if ($scope.loading) {
        return;
      }

      if (form.$invalid) {
        busService.$emit('log.error', {
          timeout: 5000,
          cause: err.code,
          msg: $translate('group.messages.form_invalid')
        });

        return;
      }

      $scope.loading = true;

      // Emit event to update group.
      $scope.updateEntity('group', $scope.group).then(
        function success(result) {
          // Update the group with data from database.
          $scope.group = result;

          // Remove spinner.
          $scope.loading = false;

          // Display message success.
          busService.$emit('log.info', {
            timeout: 3000,
            msg: $translate('group.messages.group_updated')
          });
        },
        function error(err) {
          // Display message success.
          busService.$emit('log.error', {
            cause: err.code,
            msg: $translate('group.messages.group_not_updated')
          });
        }
      );
    };
  }
]);
