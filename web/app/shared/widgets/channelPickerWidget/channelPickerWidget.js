(function() {
  "use strict";

  var app;
  app = angular.module("itkChannelPickerWidget", []);

  app.directive('channelPickerWidget', ['configuration', 'userFactory', 'channelFactory',
    function(configuration, userFactory, channelFactory) {
      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'app/shared/widgets/channelPickerWidget/channel-picker-widget.html',
        scope: {
          screen: '=',
          selectedChannels: '='
        },
        link: function(scope, element, attrs) {
          scope.displaySharingOption = configuration.sharingService.enabled;
          scope.loading = false;

          // Set default orientation and sort.
          scope.orientation = 'landscape';
          scope.showFromUser = 'all';
          scope.sort = { "created_at": "desc" };

          userFactory.getCurrentUser().then(
            function(data) {
              scope.currentUser = data;
            }
          );

          // Default pager values.
          scope.pager = {
            "size": 9,
            "page": 0
          };
          scope.hits = 0;

          // Channels to display.
          scope.channels = [];

          // Setup default search options.
          var search = {
            "fields": 'title',
            "text": '',
            "filter": {
              "bool": {
                "must": {
                  "term": {
                    "orientation":  scope.orientation
                  }
                }
              }
            },
            "sort": {
              "created_at" : {
                "order": "desc"
              }
            },
            'pager': scope.pager
          };

          /**
           * Updates the channels array by send a search request.
           */
          scope.updateSearch = function updateSearch() {
            // Get search text from scope.
            search.text = scope.search_text;

            scope.loading = true;

            channelFactory.searchChannels(search).then(
              function(data) {
                // Total hits.
                scope.hits = data.hits;

                // Extract search ids.
                var ids = [];
                for (var i = 0; i < data.results.length; i++) {
                  ids.push(data.results[i].id);
                }

                // Load slides bulk.
                channelFactory.loadChannelsBulk(ids).then(
                  function (data) {
                    scope.channels = data;

                    scope.loading = false;
                  },
                  function (reason) {
                    scope.loading = false;
                  }
                );
              }
            );
          };

          /**
           * Returns true if channel is in selected channels array.
           *
           * @param channel
           * @returns {boolean}
           */
          scope.channelSelected = function channelSelected(channel) {
            if (!scope.ikSelectedChannels) {
              return false;
            }

            var res = false;

            scope.ikSelectedChannels.forEach(function(element, index) {
              if (element.id == channel.id) {
                res = true;
              }
            });

            return res;
          };

          /**
           * Adding a channel to screen region.
           * @param channel
           *
           * @TODO: implement this!
           */
          scope.addChannel = function addChannel(channel) {
            console.log("adding channel");
            console.log(channel);
          };

          /**
           * Removing a channel from a screen region.
           * @param channel
           *
           * @TODO: implement this!
           */
          scope.removeChannel = function removeChannel(channel) {
            console.log("removing channel");
            console.log(channel);
          };

          /**
           * When the screen is loaded, set search orientation.
           */
          scope.$watch('screen', function (newVal, oldVal) {
            if (!newVal) return;

            scope.orientation = scope.screen.orientation;

            scope.updateSearch();
          });
        }
      };
    }
  ]);
}).call(this);
