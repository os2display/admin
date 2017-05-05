/**
 * @file
 * Controller admin dashboard.
 */
angular.module('adminApp').controller('AdminDashboardController', [
  'busService', '$scope', '$controller',
  function (busService, $scope, $controller) {
    'use strict';

    // Instantiate Users and Groups controllers.
    // @TODO: Handle this differently to avoid collisions.
    $controller('AdminUsersController', { $scope: $scope });
    $controller('AdminGroupsController', { $scope: $scope });

    $scope.max = 5;
    $scope.allLinks = true;
  }
]);
