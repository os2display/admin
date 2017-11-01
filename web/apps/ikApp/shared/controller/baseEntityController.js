/**
 * @file
 * Contains base entity controller.
 */

angular.module('ikApp').controller('BaseEntityController', [
  '$scope', 'userService', 'busService', 'entityType', '$timeout',
  function ($scope, userService, busService, entityType, $timeout) {
    'use strict';

    $scope.baseUnavailableGroups = [];
    $scope.userGroups = userService.getCurrentUser().groups;

    $scope.$watch(entityType, function (newValue) {
      if (newValue !== null && newValue !== undefined && newValue.hasOwnProperty('groups')) {
        $timeout(function () {
          $scope.baseUnavailableGroups = newValue.groups.reduce(function (result, element) {
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
