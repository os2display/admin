/**
 * @file
 * Contains the channel overview controller.
 */

/**
 * Directive to show the Channel overview.
 */
angular.module('ikApp').directive('ikChannelOverview', ['channelFactory', 'userFactory', 'itkLog',
  function(channelFactory, userFactory, itkLog) {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikSelectedChannels: '=',
        ikOverlay: '@'
      },
      link: function(scope) {
        scope.displaySharingOption = window.config.sharingService.enabled;
        scope.loading = false;

        scope.showFromUser = 'all';
        scope.sort = { "created_at": "desc" };

        userFactory.getCurrentUser().then(
          function(data) {
            scope.currentUser = data;
          }
        );

        // Default pager values.
        scope.pager = {
          "size": 6,
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
            "created_at" : {
              "order": 'desc'
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
            function success(data) {
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
                  itkLog.error("Kunne ikke loade søgeresultatet.", reason);
                  scope.loading = false;
                }
              );
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
            if (element.id === channel.id) {
              res = true;
            }
          });

          return res;
        };

        /**
         * Emits the slideOverview.clickSlide event.
         *
         * @param channel
         */
        scope.channelOverviewClickChannel = function channelOverviewClickChannel(channel) {
          scope.$emit('channelOverview.clickChannel', channel);
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

            scope.setSearchFilters();
          }
        };

        /**
         * Updates the search filter based on current orientation and user
         */
        scope.setSearchFilters = function setSearchFilters() {
          // Update orientation for the search.
          delete search.filter;

          if(scope.showFromUser !== 'all') {
            search.filter = {
              "bool": {
                "must": []
              }
            };
          }

          var term = {};

          if (scope.showFromUser !== 'all') {
            term.term = {user : scope.currentUser.id};
            search.filter.bool.must.push(term);
          }

          scope.updateSearch();
        };

        /**
         * Changes the sort order and updated the channels.
         *
         * @param sortField
         *   Field to sort on.
         * @param sortOrder
         *   The order to sort in 'desc' or 'asc'.
         */
        scope.setSort = function setSort(sortField, sortOrder) {
          // Only update search if sort have changed.
          if (scope.sort[sortField] === undefined || scope.sort[sortField] !== sortOrder) {
            // Update the store sort order.
            scope.sort = { };
            scope.sort[sortField] = sortOrder;

            // Update the search variable.
            search.sort = { };
            search.sort[sortField] = {
              "order": sortOrder
            };

            scope.updateSearch();
          }
        };

        scope.updateSearch();
      },
      templateUrl: '/app/shared/elements/channelOverview/channel-overview-directive.html?' + window.config.version
    };
  }
]);
