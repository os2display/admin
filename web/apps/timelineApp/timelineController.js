/**
 * @TODO: Change to use search function
 */
angular.module('timelineApp').controller('TimelineController', ['busService', 'timelineService', '$scope',
  function (busService, $scope, timelineService) {
    'use strict';

    timelineService.fetchData().then(
      function success(data) {
        scope.data = data;
      },
      function error(reason) {
        console.error(reason);
      }
    );
  }
]);