/**
 * @file
 * Contains base entity controller.
 */

angular.module('ikApp').controller('BaseEntityController', [
  '$scope', 'userService', 'busService', 'entityType', '$timeout',
  function ($scope, userService, busService, entityType, $timeout) {
    'use strict';

    // Get current user groups.
    var cleanupGetCurrentUserGroups = busService.$on('channelController.getCurrentUserGroups', function (event, groups) {
      $timeout(function () {
        $scope.userGroups = groups;

        $scope.baseUnavailableGroups = $scope[entityType].groups.reduce(function (result, element) {
          if ($scope.userGroups.findIndex(function (el) {
              return el.id === element.id;
            }) === -1) {
            result.push(element);
          }
          return result;
        }, []);
      });
    });
    userService.getCurrentUserGroups('BaseEntityController.getCurrentUserGroups');

    $scope.$on('destroy', function () {
      cleanupGetCurrentUserGroups();
    });
  }
]);
