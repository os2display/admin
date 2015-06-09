/**
 * @file
 * Contains the itkSharedChannelPickerWidget module.
 */

/**
 * Setup the module.
 */
(function () {
  'use strict';

  var app;
  app = angular.module("itkSharedChannelPickerWidget", []);

  /**
   * shared-channel-picker-widget directive.
   *
   * html parameters:
   *   screen (object): The screen to modify.
   *   region (integer): The region of the screen to modify.
   */
  app.directive('sharedChannelPickerWidget', ['sharedChannelFactory', 'itkLogFactory', 'configuration',
    function (sharedChannelFactory, itkLogFactory, configuration) {
      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'app/shared/widgets/sharedChannelPickerWidget/shared-channel-picker-widget.html?' + configuration.version,
        scope: {
          screen: '=',
          region: '='
        },
        link: function (scope, element, attrs) {
          scope.index = null;
          scope.loading = false;
          scope.pickIndexDialog = false;

          scope.sharingIndexes = [];
          sharedChannelFactory.getSharingIndexes().then(
            function success(data) {
              scope.sharingIndexes = data;
            },
            function error(reason) {
              itkLogFactory.error("Kunne ikke hente delingsindeks", reason);
            }
          );

          scope.showFromUser = 'all';
          scope.sort = {"created_at": "desc"};

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
                "must": []
              }
            },
            "sort": {
              "created_at": {
                "order": "desc"
              }
            },
            'pager': scope.pager
          };

          /**
           * Updates the channels array by send a search request.
           */
          scope.updateSearch = function updateSearch() {
            if (scope.index === null) {
              return;
            }

            // Get search text from scope.
            search.text = scope.search_text;

            scope.loading = true;
            sharedChannelFactory.searchChannels(search, scope.index.index).then(
              function success(data) {
                scope.loading = false;
                scope.hits = data.hits;
                scope.channels = data.results;
              },
              function error(reason) {
                itkLogFactory.error("Kunne ikke hente søgeresultater", reason);
                scope.loading = false;
              }
            );
          };

          /**
           * Change which index is selected.
           * @param index
           */
          scope.setIndex = function setIndex(index) {
            scope.index = index;
            scope.pickIndexDialog = false;

            scope.updateSearch();
          };

          /**
           * Returns true if channel is in channel array with region.
           *
           * @param channel
           * @returns {boolean}
           */
          scope.channelSelected = function channelSelected(channel) {
            var element;
            for (var i = 0; i < scope.screen.channel_screen_regions.length; i++) {
              element = scope.screen.channel_screen_regions[i];
              if (element.shared_channel && element.shared_channel.unique_id === channel.unique_id && element.region === scope.region) {
                return true;
              }
            }
            return false;
          };

          /**
           * Adding a channel to screen region.
           * @param channel
           *   Channel to add to the screen region.
           */
          scope.addChannel = function addChannel(channel) {
            if (channel.index === undefined || channel.index === null) {
              channel.index = scope.index.index;
            }

            scope.screen.channel_screen_regions.push({
              "id": null,
              "screen_id": scope.screen.id,
              "shared_channel": channel,
              "region": scope.region
            });
          };

          /**
           * Removing a channel from a screen region.
           * @param channel
           *   Channel to remove from the screen region.
           */
          scope.removeChannel = function removeChannel(channel) {
            var element;
            for (var i = 0; i < scope.screen.channel_screen_regions.length; i++) {
              element = scope.screen.channel_screen_regions[i];
              if (element.shared_channel && element.shared_channel.unique_id === channel.unique_id && element.region === scope.region) {
                scope.screen.channel_screen_regions.splice(i, 1);
              }
            }
          };

          /**
           * When the screen is loaded, set search orientation.
           */
          attrs.$observe('screen', function (val) {
            if (!val) return;

            // Update the search.
            scope.updateSearch();
          });
        }
      };
    }
  ]);
}).call(this);
