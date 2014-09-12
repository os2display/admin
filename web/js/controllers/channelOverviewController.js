/**
 * @file
 * Channel overview controllers.
 */

/**
 * Channels controller handles the display and selection of channels.
 */
ikApp.controller('ChannelOverviewController', ['$scope', 'channelFactory',
  function($scope, channelFactory) {
    // Set default orientation and sort.
    $scope.orientation = 'landscape';
    $scope.sort = { "created_at": "desc" };

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
      }
    };

    /**
     * Updates the channels array by send a search request.
     */
    $scope.updateSearch = function updateSearch() {
      // Get search text from scope.
      search.text = $scope.search_text;

      channelFactory.searchChannels(search).then(
        function(data) {
          $scope.channels = data;
        }
      );
    };

    // Send the default search query.
    $scope.updateSearch();

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
  }
]);
