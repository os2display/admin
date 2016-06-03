/**
 * @file
 * Data service for the time line.
 **/

angular.module('timelineApp')
  .service('timelineService', ['busService', '$q', '$http',
    function (busService, $q, $http) {
      'use strict';

      /**
       * Get time line information.
       *
       * @returns {*|Deferred}
       */
      this.fetchData = function fetchData(ids) {
        var deferred = $q.defer();

        // If no ids are requested, return empty array.
        if (!ids) {
          deferred.resolve([]);
          return deferred.promise;
        }

        // Build query string.
        var queryString = "?ids[]=" + (ids.join('&ids[]='));

        // Load bulk.
        // @TODO: Screens should be load through a mainModule screenService,
        //        that uses the busService.
        // @TODO: Decide on time-line data structure, to be shared between
        //        screen and channel time-lines.
        $http.get('/api/bulk/screen/api' + queryString).then(
          function (response) {
            var data = [];

            // Format the data to match what time-line expects
            for (var screenKey in response.data) {
              var items = [];
              var regions = [];
              var screen = response.data[screenKey];

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
                if (regions.indexOf(csr.region) === -1) {
                  regions.push(csr.region);
                }
              }

              // Format groups
              var groups = [];
              for (var i = 0; i < regions.length; i++) {
                groups.push({id: regions[i], content: "Region " + regions[i]});
              }

              data.push({
                id: screen.id,
                title: screen.title,
                items: items,
                groups: groups
              });
            }

            deferred.resolve(data);
          },
          function error(response) {
            deferred.reject(response.status);
          }
        );

        return deferred.promise;
      };
    }
  ]);

