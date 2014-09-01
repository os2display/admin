/**
 * @file
 * Channel overview controllers.
 */

/**
 * Channels controller handles the display and selection of channels.
 */
ikApp.controller('ChannelOverviewController', function($scope, channelFactory) {
  // Channels to display.
  $scope.channels = [];

  // Setup default search options.
  $scope.search = {
    "fields": 'title',
    "text": '',
    "filter": {
      "orientation":  'landscape'
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
    channelFactory.searchChannels($scope.search).then(
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
    $scope.search.filter.orientation = orientation;

    $scope.updateSearch();
  };

  /**
   * Changes the sort order and updated the channels.
   *
   * @param sortField
   *   Field to sort on.
   * @param sortOrder
   *   The order to sort in 'desc' or 'asc'.
   */
  $scope.setSort = function setSort(sortField, sortOrder) {
    $scope.search.sort = {};
    $scope.search.sort[sortField] = {
      "order": sortOrder
    };

    $scope.updateSearch();
  };
});
