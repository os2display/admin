/**
 * @file
 * Controller for the admin user page.
 */

angular.module('adminApp').controller('AdminUserController', [
  'busService', '$scope', '$timeout', 'ModalService', '$routeParams', '$location', '$controller',
  function (busService, $scope, $timeout, ModalService, $routeParams, $location, $controller) {
    'use strict';

    // Extend BaseApiController.
    $controller('BaseApiController', {$scope: $scope});

    var userRoles;

    $scope.user = null;
    $scope.userRoles = [];
    $scope.loading = true;
    $scope.forms = {};

    $scope.baseApiRequest('get', '/api/user/roles').then(
      function (roles) {
        userRoles = roles;
      }
    );

    /**
     * Adds role to userRoles.
     *
     * @param role
     */
    var addRole = function addRole(role) {
      var newRole = {
        id: role,
        title: userRoles[role] ? userRoles[role] : role
      };

      if ($scope.baseCanUpdate($scope.user)) {
        newRole.actions = [
          {
            'title': 'Fjern rolle',
            'entity': {id: role},
            'click': $scope.removeRoleFromUser
          }
        ];
      }
      $scope.userRoles.push(newRole);
    };

    /**
     * Remove role from userRoles.
     *
     * @param role
     */
    function removeRole(role) {
      var roleIndex = $scope.userRoles.findIndex(function (element) {
        return element.id === role.id;
      });

      if (roleIndex) {
        $scope.userRoles.splice(roleIndex, 1);
      }
    }

    /**
     * Show add role modal.
     */
    $scope.addRole = function addRole() {
      ModalService.showModal({
        templateUrl: "apps/adminApp/user/popup-add-roles.html",
        controller: "PopupAddRoles"
      }).then(function (modal) {
        modal.close.then(function (role) {
          if (role) {
            addRole(role);
          }
        });
      });
    };

    $scope.removeRoleFromUser = function removeRole(role) {
      alert('not implemented!');
    };

    // If id set, request that user, else use baseCurrentUser (from BaseController).
    if ($routeParams.id) {
      $scope.getEntity('user', {id: $routeParams.id}).then(
        function success(user) {
          $timeout(function () {
            $scope.user = user;

            for (var role in $scope.user.roles) {
              addRole($scope.user.roles[role]);
            }
          });
        },
        function error(err) {
          busService.$emit('log.error', {
            timeout: 5000,
            cause: err.code,
            msg: 'Brugeren kan ikke findes.'
          });

          // Redirect to dashboard.
          $location.path('/admin');
        }
      ).then(function () {
        $scope.loading = false;
      });
    }
    else {
      // Get user from BaseController.
      $scope.user = $scope.baseCurrentUser;

      // Remove spinner.
      $scope.loading = false;
    }

    /**
     * Submit form.
     */
    $scope.submitForm = function submitForm(form) {
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

      $scope.updateEntity('user', $scope.user).then(
        function success(user) {
          $scope.user = user;

          // Display message success.
          busService.$emit('log.info', {
            timeout: 3000,
            msg: 'Bruger opdateret.'
          });
        },
        function error(err) {
          // Display message success.
          busService.$emit('log.error', {
            cause: err.code,
            msg: 'Bruger kunne ikke opdateres.'
          });
        }
      ).then(function () {
        $scope.loading = false;
      });
    };
  }
]);
