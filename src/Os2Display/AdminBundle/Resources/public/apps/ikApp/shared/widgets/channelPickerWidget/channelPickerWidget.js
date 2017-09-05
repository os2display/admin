/**
 * @file
 * Contains the itkChannelPickerWidget module.
 */

/**
 * Setup the module.
 */
(function () {
  'use strict';

  var app;
  app = angular.module("itkChannelPickerWidget", []);

  /**
   * channel-picker-widget directive.
   *
   * html parameters:
   *   screen (object): The screen to modify.
   *   region (integer): The region of the screen to modify.
   */
  app.directive('channelPickerWidget', [
    'userService', 'channelFactory', 'busService',
    function (userService, channelFactory, busService) {
      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'bundles/os2displayadmin/apps/ikApp/shared/widgets/channelPickerWidget/channel-picker-widget.html?' + window.config.version,
        scope: {
          screen: '=',
          region: '='
        },
        link: function (scope) {
          scope.sharingEnabled = window.config.sharingService.enabled;
          scope.loading = false;

          scope.showFromUser = 'all';
          scope.sort = {"created_at": "desc"};

          scope.selectedGroup = null;

          // Get current user.
          scope.currentUser = userService.getCurrentUser();

          var cleanupCurrentUserGroupsListener = busService.$on('itkChannelPickerWidget.currentUserGroups', function getCurrentUserGroups(event, userGroups) {
            scope.userGroups = userGroups;
          });
          userService.getCurrentUserGroups('itkChannelPickerWidget.currentUserGroups');

          // Default pager values.
          scope.pager = {
            "size": 5,
            "page": 0
          };
          scope.hits = 0;

          // Channels to display.
          scope.channels = [];

          // Setup default search options.
          scope.search = {
            fields: 'title',
            text: '',
            filter: {
              bool: {
                must: []
              }
            },
            sort: {
              created_at: {
                order: 'desc'
              }
            },
            pager: scope.pager
          };

          /**
           * Updates the channels array by send a search request.
           */
          scope.updateSearch = function updateSearch() {
            var search = angular.copy(scope.search);

            // Get search text from scope.
            search.text = scope.search_text;

            if (scope.selectedGroup !== null) {
              search.filter.bool.must.push({
                "terms": {
                  "groups": [scope.selectedGroup.id]
                }
              });
            }

            scope.loading = true;

            channelFactory.searchChannels(search).then(
              function (data) {
                // Total hits.
                scope.hits = data.hits;

                // Extract search ids.
                var ids = [];
                for (var i = 0; i < data.results.length; i++) {
                  ids.push(data.results[i].id);
                }

                // Load slides bulk.
                channelFactory.loadChannelsBulk(ids).then(
                  function success(data) {
                    scope.channels = data;

                    scope.loading = false;
                  },
                  function error(reason) {
                    busService.$emit('log.error', {
                      'cause': reason,
                      'msg': 'Kunne ikke hente søgeresultater.'
                    });
                    scope.loading = false;
                  }
                );
              }
            );
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
              if (element.channel && element.channel.id === channel.id && element.region === scope.region) {
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
            scope.screen.channel_screen_regions.push({
              "id": null,
              "screen_id": scope.screen.id,
              "channel": channel,
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
              if (element.channel && element.channel.id === channel.id && element.region === scope.region) {
                scope.screen.channel_screen_regions.splice(i, 1);
              }
            }
          };

          scope.selectGroup = function selectGroup(group) {
            scope.pickGroupDialog = false;
            scope.selectedGroup = group;

            // Update the search.
            scope.updateSearch();
          };

          /**
           * When the screen is loaded, set search orientation.
           */
          scope.$watch('screen', function (val) {
            if (!val) {
              return;
            }

            // Update the search.
            scope.updateSearch();
          });

          /**
           * Cleanup.
           */
          scope.$on('$destroy', function () {
            cleanupCurrentUserGroupsListener();
          })
        }
      };
    }
  ]);
}).call(this);
