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
        var d = [];

        for (var screen in data) {
          var c = [];
          var regions = [];
          var screen = data[screen];

          for (var channel in screen.channel_screen_regions) {
            var csr = screen.channel_screen_regions[channel];
            var channel = csr.channel;

            var start = channel.publish_from ? channel.publish_from * 1000 : 0;
            var end   = channel.publish_to ? channel.publish_to * 1000 : 20000000000000;

            c.push({
              id: csr.id + "_" + channel.id,
              content: channel.title,
              start: start,
              end: end,
              group: csr.region
            });

            if (regions.indexOf(csr.region) == -1) {
              regions.push(csr.region);
            }
          }

          var r = [];
          for (var i = 0; i < regions.length; i++) {
            r.push({id: regions[i], content: "Region " + regions[i]});
          }

          d.push({
            id: screen.id,
            title: screen.title,
            channels: c,
            regions: r
          });
        }

        $scope.data = d;
      })
      .error(function (data, status) {
      });
  }
]);