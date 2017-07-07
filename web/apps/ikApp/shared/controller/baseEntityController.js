/**
 * @file
 * Contains base entity controller.
 */

angular.module('ikApp').controller('BaseEntityController', [
  '$scope', 'userService', 'busService', 'entityType',
  function ($scope, userService, busService, entityType) {
    'use strict';

    // Get current user groups.
    var cleanupGetCurrentUserGroups = busService.$on('channelController.getCurrentUserGroups', function (event, groups) {
      $scope.userGroups = groups;
    });
    userService.getCurrentUserGroups('channelController.getCurrentUserGroups');

    /**
     * Get unavailable (to the user) groups set for entityType.
     * @return {*}
     */
    $scope.getUnavailableGroups = function () {
      if ($scope.unavailableGroups) {
        return $scope.unavailableGroups;
      }

      if ($scope.userGroups && $scope.hasOwnProperty(entityType)) {
        $scope.unavailableGroups = $scope[entityType].groups.reduce(function (result, element) {
          if ($scope.userGroups.findIndex(function (el) {
              return el.id === element.id;
            }) === -1) {
            result.push(element);
          }
          return result;
        }, []);
      }
      return $scope.unavailableGroups;
    };

    $scope.$on('destroy', function () {
      cleanupGetCurrentUserGroups();
    });
  }
]);
