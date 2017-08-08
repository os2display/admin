/**
 * @file
 * Controller admin dashboard.
 */
angular.module('adminApp').controller('AdminDashboardController', [
  'busService', '$scope', '$controller', '$location', '$filter',
  function (busService, $scope, $controller, $location, $filter) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', { $scope: $scope });

    // Get translation filter.
    var $translate = $filter('translate');

    var hasRoleGroupAdmin = $scope.requireRole('ROLE_GROUP_ADMIN');
    var hasRoleUserAdmin = $scope.requireRole('ROLE_USER_ADMIN');
    
    // Check role.
    if (!hasRoleGroupAdmin || !hasRoleUserAdmin) {
      busService.$emit('log.error', {
        timeout: 5000,
        cause: 403,
        msg: $translate('common.error.forbidden')
      });

      if (hasRoleGroupAdmin) {
        $location.path('/admin/groups');
      }
      else if (hasRoleUserAdmin) {
        $location.path('/admin/users');
      }
      else {
        $location.path('/');
      }

      return;
    }

    // Instantiate Users and Groups controllers.
    // @TODO: Handle this differently to avoid collisions.
    $controller('AdminUsersController', { $scope: $scope });
    $controller('AdminGroupsController', { $scope: $scope });

    $scope.max = 5;
    $scope.allLinks = true;
  }
]);
