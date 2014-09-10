/**
 * @file
 * Screen overview controllers.
 */

/**
 * Screens controller handles the display and selection of screens.
 */
ikApp.controller('ScreenOverviewController', ['$scope', 'screenFactory',
  function($scope, screenFactory) {
    // Screens to display.
    $scope.screens = [];

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
     * Updates the screens array by send a search request.
     */
    $scope.updateSearch = function() {
      screenFactory.searchScreens($scope.search).then(
        function(data) {
          $scope.screens = data;
        }
      );
    };

    // Send the default search query.
    $scope.updateSearch();

    /**
     * Changes orientation and updated the screens.
     *
     * @param orientation
     *   This should either be 'landscape' or 'portrait'.
     */
    $scope.setOrientation = function(orientation) {
      $scope.search.filter.orientation = orientation;

      $scope.updateSearch();
    };

    /**
     * Changes the sort order and updated the screens.
     *
     * @param sortField
     *   Field to sort on.
     * @param sortOrder
     *   The order to sort in 'desc' or 'asc'.
     */
    $scope.setSort = function(sortField, sortOrder) {
      $scope.search.sort = {};
      $scope.search.sort[sortField] = {
        "order": sortOrder
      };

      $scope.updateSearch();
    };
  }
]);
