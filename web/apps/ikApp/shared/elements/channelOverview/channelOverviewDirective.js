/**
 * @file
 * Contains the channel overview controller.
 */

/**
 * Directive to show the Channel overview.
 */
angular.module('ikApp').directive('ikChannelOverview', [
  'busService',
  function(busService) {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikSelectedChannels: '=',
        ikOverlay: '@'
      },
      controller: function ($scope, $filter, $controller, channelFactory) {
        $controller('BaseSearchController', {$scope: $scope});

        // Get filter selection "all/mine" from localStorage.
        $scope.showFromUser = localStorage.getItem('overview.channel.search_filter_default') ?
          localStorage.getItem('overview.channel.search_filter_default') :
          'all';

        $scope.displaySharingOption = window.config.sharingService.enabled;

        // Channels to display.
        $scope.channels = [];

        /**
         * Updates the channels array by send a search request.
         */
        $scope.updateSearch = function updateSearch() {
          // Get search text from scope.
          $scope.baseQuery.text = $scope.search_text;

          $scope.loading = true;

          channelFactory.searchChannels($scope.baseQuery).then(
            function success(data) {
              // Total hits.
              $scope.hits = data.hits;

              // Extract search ids.
              var ids = [];
              for (var i = 0; i < data.results.length; i++) {
                ids.push(data.results[i].id);
              }

              // Load slides bulk.
              channelFactory.loadChannelsBulk(ids).then(
                function success(data) {
                  $scope.channels = data;

                  $scope.loading = false;
                },
                function error(reason) {
                  busService.$emit('log.error', {
                    'cause': reason,
                    'msg': 'Kunne ikke loade søgeresultatet.'
                  });
                  $scope.loading = false;
                }
              );
            }
          );
        };

        /**
         * Update search result on channel deletion.
         */
        $scope.$on('channel-deleted', function() {
          $scope.updateSearch();
        });

        /**
         * Returns true if channel is in selected channels array.
         *
         * @param channel
         * @returns {boolean}
         */
        $scope.channelSelected = function channelSelected(channel) {
          if (!$scope.ikSelectedChannels) {
            return false;
          }

          var res = false;

          $scope.ikSelectedChannels.forEach(function(element) {
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
        $scope.channelOverviewClickChannel = function channelOverviewClickChannel(channel) {
          $scope.$emit('channelOverview.clickChannel', channel);
        };

        /**
         * Updates the search filter based on current orientation and user
         */
        $scope.setSearchFilters = function setSearchFilters() {
          delete $scope.baseQuery.filter;

          // No groups selected and "all" selected => select all groups and my.
          var selectedGroupIds = $filter('filter')($scope.userGroups, { selected: true }, true).map(function (group) {
            return group.id;
          });

          $scope.baseQuery.filter = $scope.baseBuildSearchFilter(selectedGroupIds);

          $scope.pager.page = 0;

          $scope.updateSearch();
        };

        /**
         * Is the channel scheduled for now?
         *
         * @param channel
         */
        $scope.channelScheduledNow = function channelScheduledNow(channel) {
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
        $scope.getScheduledText = function getScheduledText(channel) {
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

        $scope.setSearchFilters();
      },
      templateUrl: '/apps/ikApp/shared/elements/channelOverview/channel-overview-directive.html?' + window.config.version
    };
  }
]);
