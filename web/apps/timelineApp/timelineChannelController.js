/**
 * @file
 * @TODO: Change to use search function.
 * @TODO: Move all this search logic into a service on the BUS.
 */
angular.module('timelineApp').controller('TimelineChannelController', ['busService', 'timelineService', '$scope',
  function (busService, timelineService, $scope) {
    'use strict';

    $scope.loading = false;

    // Set default values.
    $scope.sort = { "created_at": "desc" };

    // Default pager values.
    $scope.pager = {
      "size": 6,
      "page": 0
    };
    $scope.hits = 0;

    // Screens to display.
    $scope.screens = [];

    // Setup default search options.
    var search = {
      "fields": [ 'title' ],
      "text": '',
      "sort": {
        "created_at" : {
          "order": "desc"
        }
      },
      'pager': $scope.pager
    };

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