/**
 * @file
 * Contains the channel overview controller.
 */

/**
 * Directive to show the Channel overview.
 */
angular.module('ikApp').directive('ikChannelOverview', ['channelFactory', 'userFactory', 'busService', '$filter',
  function(channelFactory, userFactory, busService, $filter) {
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

        scope.sort = { "created_at": "desc" };

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
                  busService.$emit('log.error', {
                    'cause': reason,
                    'msg': 'Kunne ikke loade søgeresultatet.'
                  });
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
          // Save selection in localStorage.
          localStorage.setItem('overview.channel.search_filter_default', user);

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

        /**
         * Is the channel scheduled for now?
         *
         * @param channel
         */
        scope.channelScheduledNow = function channelScheduledNow(channel) {
          var now = new Date();
          var todayDayNumber = now.getDay();
          var todayHour = now.getHours();
          now = parseInt(now.getTime() / 1000);

          if (channel.hasOwnProperty('publish_from') && now < channel.publish_from) {
            return false;
          }
          else if (channel.hasOwnProperty('publish_to') && now > channel.publish_to) {
            return false;
          }
          else if (channel.hasOwnProperty('schedule_repeat') && channel.schedule_repeat) {
            if (channel.hasOwnProperty('schedule_repeat_days') && channel.schedule_repeat_days.length > 0) {
              for (var i = 0; i < channel.schedule_repeat_days.length; i++) {
                if (todayDayNumber === channel.schedule_repeat_days[i].id) {
                  if (channel.hasOwnProperty('schedule_repeat_from') && todayHour < channel.schedule_repeat_from) {
                    return false;
                  }
                  if (channel.hasOwnProperty('schedule_repeat_to') && todayHour > channel.schedule_repeat_to) {
                    return false;
                  }
                }
              }
            }
            else {
              return false;
            }
          }

          return true;
        };

        /**
         * Get scheduled text for channel.
         *
         * @param channel
         */
        scope.getScheduledText = function getScheduledText(channel) {
          var text = '';

          if (channel.hasOwnProperty('publish_from')) {
            text = text + "Udgivet fra: " + $filter('date')(channel.publish_from * 1000, "dd/MM/yyyy HH:mm") + ".<br/>";
          }

          if (channel.hasOwnProperty('publish_to')) {
            text = text + "Udgivet til: " + $filter('date')(channel.publish_to * 1000, "dd/MM/yyyy HH:mm") + ".<br/>";
          }

          if (channel.hasOwnProperty('schedule_repeat') && channel.schedule_repeat) {
            text = text + "Vises disse dage:<br/>";
            for (var i = 0; i < channel.schedule_repeat_days.length; i++) {
              text = text + channel.schedule_repeat_days[i].name + " ";
            }
            text = text + "<br/>"
            if (channel.hasOwnProperty('schedule_repeat_from')) {
              text = text + "Fra: " + channel.schedule_repeat_from + ":00<br/>";
            }
            if (channel.hasOwnProperty('schedule_repeat_to')) {
              text = text + "Til: " + channel.schedule_repeat_to + ":00<br/>";
            }
          }

          return text;
        };

        userFactory.getCurrentUser().then(
          function (data) {
            scope.currentUser = data;

            // Get filter selection "all/mine" from localStorage.
            scope.showFromUser = localStorage.getItem('overview.channel.search_filter_default') ?
              localStorage.getItem('overview.channel.search_filter_default') :
              'all';

            // Updated search filters (build "mine" filter with user id). It
            // will trigger an search update.
            scope.setSearchFilters();
          }
        );
      },
      templateUrl: '/app/shared/elements/channelOverview/channel-overview-directive.html?' + window.config.version
    };
  }
]);
