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
    $http.get('/api/screens/channel-bulk' + queryString)
      .success(function (data, status) {
        var d = [];

        // Format the data to match what timeline expects
        for (var screenKey in data) {
          var c = [];
          var regions = [];
          var screen = data[screenKey];

          for (var channelKey in screen.channel_screen_regions) {
            var csr = screen.channel_screen_regions[channelKey];
            var channel = csr.channel;

            c.push({
              channel_id: channel.id,
              id: csr.id + "_" + channel.id,
              content: channel.title,
              group: csr.region,
              subgroup: csr.id + "_" + channel.id,
              start: channel.publish_from * 1000,
              end: channel.publish_to * 1000,
              schedule_repeat: channel.schedule_repeat,
              schedule_repeat_days: channel.schedule_repeat_days,
              schedule_repeat_from: channel.schedule_repeat_from,
              schedule_repeat_to: channel.schedule_repeat_to
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