/**
 * @file
 * Channels controller handles the display and selection of channels.
 */


ikApp.controller('ChannelOverviewController', function($scope, channelFactory) {
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
  var updateChannels = function() {

    channelFactory.searchChannels($scope.search).then(
      function(data) {
        $scope.channels = data;
      }
    );
  };

  // Send the default search query.
  updateChannels();

  /**
   * Changes orientation and updated the channels.
   *
   * @param orientation
   *   This should either be 'landscape' or 'portrait'.
   */
  $scope.setOrientation = function(orientation) {
    $scope.search.filter.orientation = orientation;

    updateChannels();
  };

  /**
   * Changes the sort order and updated the channels.
   *
   * @param sort
   *   Field to sort on.
   * @param sortOrder
   *   The order to sort in 'desc' or 'asc'.
   */
  $scope.setSort = function(sortfield, sortOrder) {
    var sortSetup = new Object();
    sortSetup[sortfield] = {
      "order": sortOrder
    }
    $scope.search.sort[0] = sortSetup;

    updateChannels();
  };


  /**
   * Perform search
   */
  $scope.updateSearch = function() {
    updateChannels();
  };

});
