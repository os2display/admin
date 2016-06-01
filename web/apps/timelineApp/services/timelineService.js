/**
 * @file
 * Data service for the time line.
 **/

angular.module('timelineApp')
  .service('timelineService', ['$q', '$http', function ($q, $http) {
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

    /**
     * Get time line information.
     *
     * @returns {*|Deferred}
     */
    this.fetchData = function fetchData() {

      var deferred = $q.defer();

      // Load bulk.
      // @TODO: Move data processing to a timeline service!
      // @TODO: Decide on timeline data structure, to be shared between screen and channel timelines.
      $http.get('/api/timeline/screens-bulk' + queryString).then(
        function (response) {
          var d = [];

          // Format the data to match what time-line expects
          for (var screenKey in response.data) {
            var items = [];
            var regions = [];
            var screen = data[screenKey];

            for (var channelKey in screen.channel_screen_regions) {
              var csr = screen.channel_screen_regions[channelKey];
              var channel = csr.channel;

              items.push({
                // Ensure unique id: ChannelScreenRegion + channel id.
                id: csr.id + "_" + channel.id,
                // Text displayed in time-line item.
                content: channel.title,
                // Which group should the item belong to?
                group: csr.region,
                // Subgroup is used to make sure channel items with different
                // unique ids are gathered on the same line in the time line.
                subgroup: csr.id + "_" + channel.id,
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

          deferred.resolve(d);
        },
        function error(response) {
          deferred.reject(response.status);
        }
      );

      return deferred.promise;
    };

  }]);

