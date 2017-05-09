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

    /**
     * Set user on scope.
     *
     * @param user
     */
    function setUser(user) {
      $scope.user = user;
      $scope.userHeading = $scope.user.firstname ? $scope.user.firstname + ' ' + $scope.user.lastname : $scope.user.email;
    }

    // Load roles, then load user.
    $scope.baseApiRequest('get', '/api/user/roles').then(
      function (roles) {
        userRoles = roles;
      }
    ).then(function () {
      // If id set, request that user, else use baseCurrentUser (from BaseController).
      if ($routeParams.id) {
        $scope.getEntity('user', {id: $routeParams.id}).then(
          function success(user) {
            $timeout(function () {

              setUser(user);

              for (var role in $scope.user.roles) {
                addRoleToDisplayList($scope.user.roles[role]);
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
        setUser($scope.baseCurrentUser);

        // Remove spinner.
        $scope.loading = false;

        for (var role in $scope.user.roles) {
          addRoleToDisplayList($scope.user.roles[role]);
        }
      }
    });

    /**
     * Remove role from user.
     *
     * @param roleToRemove
     */
    $scope.removeRoleFromUser = function (roleToRemove) {
      $scope.loading = true;

      var roles = [];
      for (var role in $scope.user.roles) {
        if ($scope.user.roles[role] !== roleToRemove)
        roles.push($scope.user.roles[role]);
      }

      var user = angular.copy($scope.user);
      user.roles = roles;

      $scope.loading = true;

      // Load roles, then load user.
      $scope.baseApiRequest('put', '/api/user/' + $scope.user.id, user).then(
        function (user) {
          $timeout(function () {
            // Get user from BaseController.
            setUser(user);

            $scope.userRoles = [];

            for (var role in $scope.user.roles) {
              addRoleToDisplayList($scope.user.roles[role]);
            }
          });
        },
        function error(err) {
          console.error(err);
        }
      ).then(function () {
        $scope.loading = false;
      });
    };

    /**
     * Adds role to userRoles.
     *
     * @param role
     */
    var addRoleToDisplayList = function addRoleToDisplayList(role) {
      var f = $scope.userRoles.find(function (element) {
        return element.id === role
      });

      if (!f) {
        var actions = [];

        if ($scope.baseCanUpdate($scope.user)) {
          actions.push({
            title: 'Fjern rolle fra bruger',
            click: $scope.removeRoleFromUser,
            entity: role
          });
        }
        var newRole = {
          id: role,
          title: userRoles[role] ? userRoles[role] : role,
          actions: actions
        };
        $scope.userRoles.push(newRole);
      }
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
            // Get user from BaseController.
            setUser(user);

            $scope.userRoles = [];

            for (var role in $scope.user.roles) {
              addRoleToDisplayList($scope.user.roles[role]);
            }
          });
        },
        function error(err) {
          console.error(err);
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
