/**
 * @file
 * Contains the channel overview controller.
 */

/**
 * Directive to show the Channel overview.
 */
ikApp.directive('ikChannelOverview', function() {
  "use strict";

  return {
    restrict: 'E',
    scope: {
      ikSelectedChannels: '=',
      ikHideFilters: '=',
      ikFilter: '@',
      ikOverlay: '@'
    },
    controller: function($scope, channelFactory) {
      // Set default orientation and sort.
      $scope.orientation = 'landscape';
      $scope.sort = { "created_at": "desc" };

      // Default pager values.
      $scope.pager = {
        "size": 9,
        "page": 0
      };
      $scope.hits = 0;

      // Channels to display.
      $scope.channels = [];

      // Setup default search options.
      var search = {
        "fields": 'title',
        "text": '',
        "filter": {
          "bool": {
            "must": {
              "term": {
                "orientation":  $scope.orientation
              }
            }
          }
        },
        "sort": {
          "created_at" : {
            "order": "desc"
          }
        },
        'pager': $scope.pager
      };

      /**
       * Updates the channels array by send a search request.
       */
      $scope.updateSearch = function updateSearch() {
        // Get search text from scope.
        search.text = $scope.search_text;

        channelFactory.searchChannels(search).then(
          function(data) {
            // Total hits.
            $scope.hits = data.hits;

            // Extract search ids.
            var ids = [];
            for (var i = 0; i < data.results.length; i++) {
              ids.push(data.results[i].id);
            }

            // Load slides bulk.
            channelFactory.loadChannelsBulk(ids).then(
              function (data) {
                $scope.channels = data;
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
      $scope.channelSelected = function channelSelected(channel) {
        if (!$scope.ikSelectedChannels) {
          return false;
        }

        var res = false;

        $scope.ikSelectedChannels.forEach(function(element, index) {
          if (element.id == channel.id) {
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
       * Changes orientation and updated the channels.
       *
       * @param orientation
       *   This should either be 'landscape' or 'portrait'.
       */
      $scope.setOrientation = function setOrientation(orientation) {
        if ($scope.orientation !== orientation) {
          $scope.orientation = orientation;

          // Update search query.
          search.filter.bool.must.term.orientation = $scope.orientation;

          $scope.updateSearch();
        }
      };

      /**
       * Changes the sort order and updated the channels.
       *
       * @param sort_field
       *   Field to sort on.
       * @param sort_order
       *   The order to sort in 'desc' or 'asc'.
       */
      $scope.setSort = function setSort(sort_field, sort_order) {
        // Only update search if sort have changed.
        if ($scope.sort[sort_field] === undefined || $scope.sort[sort_field] !== sort_order) {
          // Update the store sort order.
          $scope.sort = { };
          $scope.sort[sort_field] = sort_order;

          // Update the search variable.
          search.sort = { };
          search.sort[sort_field] = {
            "order": sort_order
          };

          $scope.updateSearch();
        }
      };

      // Set filter if parameter ikFilter is set.
      if ($scope.ikFilter) {
        $scope.setOrientation($scope.ikFilter);
      }

      // Send the default search query.
      $scope.updateSearch();
    },
    templateUrl: '/partials/directives/channel-overview-directive.html'
  };
});