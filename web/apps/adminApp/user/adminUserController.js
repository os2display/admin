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

    $scope.user = null;
    $scope.userRoles = [];
    $scope.userGroups = [];
    $scope.loading = true;
    $scope.forms = {};

    /**
     * Adds role to userRoles.
     *
     * @param key
     * @param role
     */
    var addRoleToDisplayList = function addRoleToDisplayList(key, role) {
      var actions = [];

      if ($scope.baseCanUpdate($scope.user)) {
        actions.push({
          title: 'Fjern rolle fra bruger',
          click: $scope.removeRoleFromUser,
          entity: key
        });
      }
      var newRole = {
        id: key,
        title: role,
        actions: actions
      };
      $scope.userRoles.push(newRole);
    };

    /**
     * Add role to user.
     *
     * @param roleToAdd
     */
    var addRoleToUser = function addRoleToUser(roleToAdd) {
      var roles = [];
      for (var role in $scope.user.roles) {
        roles.push(role);
      }

      if (roles.indexOf(roleToAdd.id) === -1) {
        roles.push(roleToAdd.id)
      }

      var user = angular.copy($scope.user);
      user.roles = roles;

      $scope.loading = true;

      // Load roles, then load user.
      $scope.baseApiRequest('put', '/api/user/' + $scope.user.id, user).then(
        function (user) {
          $timeout(function () {
            setUser(user);
          });
        },
        function error(err) {
          busService.$emit('log.error', {
            cause: err.code,
            msg: 'Kunne ikke tilføje rolle til bruger.'
          });
        }
      ).then(function () {
        $scope.loading = false;
      });
    };

    /**
     * Sets user and userRoles.
     *
     * @param user
     */
    function setUser(user) {
      $scope.user = user;
      $scope.userRoles = [];
      $scope.userGroups = [];

      for (var role in $scope.user.roles) {
        addRoleToDisplayList(role, $scope.user.roles[role]);
      }

      for (var group in $scope.user.groups) {
        var group = $scope.user.groups[group];

        $scope.userGroups.push({
          id: group.id,
          title: group.displayName,
          url: '#/admin/group/' + group.id
        });
      }
    }

    // If id set, request that user, else use baseCurrentUser (from BaseController).
    if ($routeParams.id) {
      $scope.getEntity('user', {id: $routeParams.id}).then(
        function success(user) {
          $timeout(function () {
            setUser(user);
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
      // Remove spinner.
      $scope.loading = false;

      setUser($scope.baseCurrentUser);
    }

    /**
     * Remove role from user.
     *
     * @param roleToRemove
     */
    $scope.removeRoleFromUser = function (roleToRemove) {
      $scope.loading = true;

      var roles = [];
      for (var role in $scope.user.roles) {
        if (role !== roleToRemove) {
          roles.push(role);
        }
      }

      var user = angular.copy($scope.user);
      user.roles = roles;

      // Load roles, then load user.
      $scope.baseApiRequest('put', '/api/user/' + $scope.user.id, user).then(
        function (user) {
          $timeout(function () {
            setUser(user);
          });
        },
        function error(err) {
          busService.$emit('log.error', {
            cause: err.code,
            msg: 'Kunne ikke fjerne rolle fra bruger.'
          });
        }
      ).then(function () {
        $scope.loading = false;
      });
    };

    /**
     * Show add role modal.
     */
    $scope.showAddRoleModal = function showAddRoleModal() {
      ModalService.showModal({
        templateUrl: "apps/adminApp/user/popup-add-role-to-user.html",
        controller: "PopupAddRoleToUser",
        inputs: {
          options: {
            type: 'user/roles',
            list: $scope.userRoles,
            heading: 'Søg efter roller',
            searchPlaceholder: '',
            clickCallback: addRoleToUser
          }
        }
      }).then(function (modal) {
        modal.close.then(function () {
        });
      });
    };

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
          setUser(user);

          // Display message success.
          busService.$emit('log.info', {
            timeout: 3000,
            msg: 'Bruger opdateret.'
          });
        },
        function error(err) {
          if (err.code === 409) {
            busService.$emit('log.error', {
              cause: err.code,
              msg: 'Bruger kunne ikke opdateres. Email eksisterer allerede.'
            });
          }
          else {
            busService.$emit('log.error', {
              cause: err.code,
              msg: 'Bruger kunne ikke opdateres.'
            });
          }
        }
      ).then(function () {
        $scope.loading = false;
      });
    };
  }
]);
