/**
 * @file
 * Contains the channel sharing overview controller.
 */

/**
 * Directive to show the Channel Sharing overview.
 */
ikApp.directive('ikChannelSharingOverview', ['sharedChannelFactory', 'userFactory', 'configuration', '$timeout',
  function(sharedChannelFactory, userFactory, configuration, $timeout) {
    "use strict";

    return {
      restrict: 'E',
      scope: {
        ikSelectedChannels: '=',
        ikHideFilters: '=',
        ikFilter: '@',
        ikOverlay: '@'
      },
      link: function(scope, element, attrs) {
        scope.index = {};
        scope.loading = false;

        scope.displaySharingOption = configuration.sharingService.enabled;
        scope.sharingIndexes = [];
        sharedChannelFactory.getSharingIndexes().then(function(data) {
          scope.sharingIndexes = data;
        });

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
          sharedChannelFactory.searchChannels(search, scope.index.index).then(
            function(data) {
              scope.loading = false;
              scope.hits = data.hits;
              scope.channels = data.results;
            },
            function (reason) {
              scope.loading = false;
            }
          );
        };

        /**
         * Update search result on channel deletion.
         */
        scope.$on('channel-deleted', function() {
          scope.updateSearch();
        });

        /**
         *
         */
        scope.updatedIndex = function updatedIndex() {
          scope.updateSearch();
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

          scope.ikSelectedChannels.forEach(function(element) {
            if (element.id == channel.id) {
              res = true;
            }
          });

          return res;
        };

        /**
         * Emits the channelSharingOverview.clickChannel event.
         *
         * @param channel
         */
        scope.clickChannel = function clickChannel(channel) {
          scope.$emit('channelSharingOverview.clickChannel', channel);
        };

        /**
         * Changes orientation and updated the channels.
         *
         * @param orientation
         *   This should either be 'landscape' or 'portrait'.
         */
        scope.setOrientation = function setOrientation(orientation) {
          if (scope.orientation !== orientation) {
            scope.orientation = orientation;

            scope.updateSearch();
          }
        };

        /**
         * Changes if all slides are shown or only slides belonging to current user
         *
         * @param user
         *   This should either be 'mine' or 'all'.
         */
        scope.setUser = function setUser(user) {
          if (scope.showFromUser !== user) {
            scope.showFromUser = user;

            scope.updateSearch();
          }
        };


        /**
         * Updates the search filter based on current orientation and user
         */
        scope.setSearchFilters = function setSearchFilters() {
          // Update orientation for the search.
          delete search.filter;

          if(scope.orientation !== 'all' || scope.showFromUser !== 'all') {
            search.filter = {
              "bool": {
                "must": []
              }
            }
          }

          if (scope.orientation !== 'all') {
            var term = {};
            term.term = {orientation : scope.orientation};
            search.filter.bool.must.push(term);
          }

          if (scope.showFromUser !== 'all') {
            var term = {};
            term.term = {user : scope.currentUser.id};
            search.filter.bool.must.push(term);
          }

          scope.updateSearch();
        };

        /**
         * Changes the sort order and updated the channels.
         *
         * @param sort_field
         *   Field to sort on.
         * @param sort_order
         *   The order to sort in 'desc' or 'asc'.
         */
        scope.setSort = function setSort(sort_field, sort_order) {
          // Only update search if sort have changed.
          if (scope.sort[sort_field] === undefined || scope.sort[sort_field] !== sort_order) {
            // Update the store sort order.
            scope.sort = { };
            scope.sort[sort_field] = sort_order;

            // Update the search variable.
            search.sort = { };
            search.sort[sort_field] = {
              "order": sort_order
            };

            scope.updateSearch();
          }
        };

        // Set filter if parameter ikFilter is set.
        if (scope.ikFilter) {
          scope.setOrientation(scope.ikFilter);
        }
      },
      templateUrl: '/partials/directives/channel-sharing-overview-directive.html'
    };
  }
]);
