/**
 * @TODO: Change to use search function 
 */
angular.module('timelineApp').controller('TimelineController', ['busService', '$scope', '$http',
  function (busService, $scope, $http) {
    'use strict';

    // Build query string.
    var queryString = "?";
    for (var i = 0; i < 10; i++) {
      queryString = queryString + "ids[]=" + i;
      if (i < 9) {
        queryString = queryString + "&"
      }
    }

    // Load bulk.
    $http.get('/api/screens/bulk' + queryString)
      .success(function (data, status) {
        console.log(data);

        var d = [];

        for (var screen in data) {
          var c = [];
          var screen = data[screen];

          for (var channel in screen.channel_screen_regions) {
            var channel = screen.channel_screen_regions[channel].channel;

            var start = channel.publish_from ? channel.publish_from * 1000 : 0;
            var end   = channel.publish_to ? channel.publish_to * 1000 : 10000000000000;

            c.push({
              id: channel.id,
              content: channel.title,
              start: start,
              end: end
            });
          }

          d.push({
            id: screen.id,
            title: screen.title,
            channels: c
          });
        }

        $scope.data = d;

        console.log(d);
      })
      .error(function (data, status) {
      });
  }
]);