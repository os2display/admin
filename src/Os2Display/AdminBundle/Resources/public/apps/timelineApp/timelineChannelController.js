/**
 * @file
 * Controller for the channel overview time-line.
 */
angular.module('timelineApp').controller('TimelineChannelController', ['busService', '$scope', '$controller',
  function (busService, $scope, $controller) {
    'use strict';

    $scope.searchType = 'Os2Display\\CoreBundle\\Entity\\Channel';
    $controller('TimelineBaseController', {$scope: $scope});
  }
]);
