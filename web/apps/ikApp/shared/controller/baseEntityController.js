/**
 * @file
 * Contains base entity controller.
 */

angular.module('ikApp').controller('BaseEntityController', [
  '$scope', 'userService', 'busService', 'entityType', '$timeout',
  function ($scope, userService, busService, entityType, $timeout) {
    'use strict';

    $scope.baseUnavailableGroups = [];
    $scope.userGroups = userService.getCurrentUser().userGroups;

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
  }
]);
