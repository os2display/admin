/**
 * @file
 * Controller admin dashboard.
 */
angular.module('adminApp').controller('AdminDashboardController', [
  'busService', '$scope', '$controller', '$timeout',
  function (busService, $scope, $controller, $timeout) {
    'use strict';

    // Instantiate Users and Groups controllers.
    $controller('AdminUsersController', { busService: busService, $scope: $scope });
    $controller('AdminGroupsController', { busService: busService, $scope: $scope });

    $scope.max = 5;
    $scope.allLinks = true;
  }
]);
