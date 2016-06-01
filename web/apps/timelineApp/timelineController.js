/**
 * @TODO: Change to use search function
 */
angular.module('timelineApp').controller('TimelineController', ['busService', '$scope', '$http',
  function (busService, $scope, $http) {
    'use strict';

    // Build query string.
    // @TODO: Replace with search
    var queryString = "?";
    for (var i = 0; i < 10; i++) {
      queryString = queryString + "ids[]=" + i;
      if (i < 9) {
        queryString = queryString + "&"
      }
    }

    // Load bulk.
    // @TODO: Move data processing to a timeline service!
    // @TODO: Decide on timeline data structure, to be shared between screen and channel timelines.
    $http.get('/api/timeline/screens-bulk' + queryString)
      .success(function (data, status) {
        var d = [];

        // Format the data to match what timeline expects
        for (var screenKey in data) {
          var items = [];
          var regions = [];
          var screen = data[screenKey];

          for (var channelKey in screen.channel_screen_regions) {
            var csr = screen.channel_screen_regions[channelKey];
            var channel = csr.channel;

            items.push({
              id: csr.id + "_" + channel.id,                            // Ensure unique id: ChannelScreenRegion + channel id
              content: channel.title,                                   // Text displayed in timeline item
              group: csr.region,                                        // Which group should the item belong to?
              subgroup: csr.id + "_" + channel.id,                      // Subgroup is used to make sure channel items with different unique ids are gathered on the same line in the timeline.
              start: channel.publish_from * 1000,
              end: channel.publish_to * 1000,
              schedule_repeat: channel.schedule_repeat,
              schedule_repeat_days: channel.schedule_repeat_days,
              schedule_repeat_from: channel.schedule_repeat_from,
              schedule_repeat_to: channel.schedule_repeat_to,
              redirect_url: '/channel/' + channel.id
            });

            // Add region if not already.
            if (regions.indexOf(csr.region) == -1) {
              regions.push(csr.region);
            }
          }

          // Format groups
          var groups = [];
          for (var i = 0; i < regions.length; i++) {
            groups.push({id: regions[i], content: "Region " + regions[i]});
          }

          d.push({
            id: screen.id,
            title: screen.title,
            items: items,
            groups: groups
          });
        }

        $scope.data = d;
      })
      .error(function (data, status) {
      });
  }
]);