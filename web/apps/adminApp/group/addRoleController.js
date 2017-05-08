/**
 * @file
 * Controller for the popup: create group.
 */

angular.module('adminApp').controller('AddUsersController', [
  'busService', '$scope', '$timeout', 'close', '$controller',
  function (busService, $scope, $timeout, close, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseApiController', {$scope: $scope});


  }
]);
