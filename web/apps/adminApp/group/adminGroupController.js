/**
 * @file
 * Controller for the admin groups page.
 */

angular.module('adminApp').controller('AdminGroupController', [
  'busService', '$scope', '$timeout', 'ModalService', '$routeParams', '$location', '$controller',
  function (busService, $scope, $timeout, ModalService, $routeParams, $location, $controller) {
    'use strict';

    // Extend
    $controller('BaseApiController', {$scope: $scope});

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
          entity.user.roles = userGroup.roles;

          $scope.baseRemoveElementFromList($scope.users, entity.user, 'id');
          addToUsers(entity.user);
        },
        function error(err) {
          console.error(err);
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
            $scope.baseRemoveElementFromList($scope.users, user, 'id');
          });
        },
        function error(err) {
          console.error(err);
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

      $scope.baseApiRequest('get', '/api/user/' + user.id + '/group/' + $scope.group.id).then(
        function success(group) {
          if ($scope.baseCanUpdate($scope.group)) {
            for (var roleId in groupRoles) {
              if (group.roles.indexOf(roleId) === -1) {
                var roleName = groupRoles[roleId];

                actions.push({
                  title: 'Sæt gruppe role: ' + roleName,
                  click: $scope.setRoleToUser,
                  entity: {
                    user: user,
                    roles: [ roleId ]
                  }
                });
              }
            }

            actions.push({
              title: 'Fjern bruger fra gruppe',
              click: $scope.removeUserFromGroup,
              entity: user
            });
          }

          var text = "";

          for (var role in group.roles) {
            text = text + "(" + groupRoles[group.roles[role]] + ") ";
          }

          $scope.users.push({
            id: user.id,
            url: '/admin/user/' + user.id,
            title: user.firstname ? user.firstname + (user.lastname ? " " + user.lastname : '') : user.username,
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
            msg: 'Gruppe kan ikke findes.'
          });

          // Redirect to dashboard.
          $location.path('/admin');
        }
      );
    });

    $scope.showAddUserModal = function showAddUserModal() {
      ModalService.showModal({
        templateUrl: "apps/adminApp/group/popup-add-user.html",
        controller: "PopupAddUser",
        inputs: {
          group: angular.copy($scope.group),
          addedUser: addToUsers
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
          msg: 'Ugyldigt input.'
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
            msg: 'Gruppe opdateret.'
          });
        },
        function error(err) {
          // Display message success.
          busService.$emit('log.error', {
            cause: err.code,
            msg: 'Gruppe kunne ikke opdateres.'
          });
        }
      );
    };
  }
]);
