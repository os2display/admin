/**
 * @file
 * Controller for the channel overview time-line.
 */
angular.module('timelineApp').controller('TimelineScreenController', ['busService', '$scope', '$controller',
  function (busService, $scope, $controller) {
    'use strict';

    $scope.searchType = 'Indholdskanalen\\MainBundle\\Entity\\Screen';
    $controller('TimelineBaseController', {$scope: $scope});
  }
]);