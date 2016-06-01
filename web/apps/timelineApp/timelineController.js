/**
 * @file
 * @TODO: Change to use search function.
 * @TODO: Move all this search logic into a service on the BUS.
 */
angular.module('timelineApp').controller('TimelineController', ['busService', 'timelineService', '$scope',
  function (busService, timelineService, $scope) {
    'use strict';

    timelineService.fetchData().then(
      function success(data) {
        $scope.data = data;
      },
      function error(reason) {
        console.error(reason);
      }
    );
  }
]);