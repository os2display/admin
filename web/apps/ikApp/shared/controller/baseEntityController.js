/**
 * @file
 * Contains base entity controller.
 */

angular.module('ikApp').controller('BaseEntityController', [
  '$scope', 'userService', 'busService', 'entityType', '$timeout',
  function ($scope, userService, busService, entityType, $timeout) {
    'use strict';

    $scope.baseUnavailableGroups = [];

    // Get current user groups.
    var cleanupGetCurrentUserGroups = busService.$on('BaseEntityController.getCurrentUserGroups', function (event, groups) {
      $timeout(function () {
        $scope.userGroups = groups;
      });
    });
    userService.getCurrentUserGroups('BaseEntityController.getCurrentUserGroups');

    $scope.$watchGroup([entityType, 'userGroups'], function (newValues) {
      if (newValues && newValues[0] && newValues[0].hasOwnProperty('groups') && newValues[1]) {
        $timeout(function () {
          $scope.baseUnavailableGroups = $scope[entityType].groups.reduce(function (result, element) {
            if ($scope.userGroups.findIndex(function (el) {
                return el.id === element.id;
              }) === -1) {
              result.push(element);
            }
            return result;
          }, []);
        });
      }
    });

    $scope.$on('destroy', function () {
      cleanupGetCurrentUserGroups();
    });
  }
]);
