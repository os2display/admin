/**
 * @file
 * Controller admin dashboard.
 */
angular.module('adminApp').controller('AdminDashboardController', [
  'busService', '$scope', '$controller',
  function (busService, $scope, $controller) {
    'use strict';

    // Instantiate Users and Groups controllers.
    $controller('AdminUsersController', { busService: busService, $scope: $scope });
    $controller('AdminGroupsController', { busService: busService, $scope: $scope });

    $scope.max = 5;
    $scope.allLinks = true;
  }
]);
