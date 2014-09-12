/**
 * @file
 * Screen overview controllers.
 */

/**
 * Screens controller handles the display and selection of screens.
 */
ikApp.controller('ScreenOverviewController', ['$scope', 'screenFactory',
  function($scope, screenFactory) {
    // Set default values.
    $scope.orientation = 'all';
    $scope.sort = { "created_at": "desc" };


    // Screens to display.
    $scope.screens = [];

    // Setup default search options.
    var search = {
      "fields": [ 'title' ],
      "text": '',
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
      screenFactory.searchScreens(search).then(
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
      $scope.orientation = orientation;

      // Update orientation for the search.
      delete search.filter;
      if (orientation !== 'all') {
        search.filter = {
          "bool": {
            "must": {
              "term": {
                "orientation": orientation
              }
            }
          }
        };
      }

      $scope.updateSearch();
    };

    /**
     * Changes the sort order and updated the screens.
     *
     * @param sort_field
     *   Field to sort on.
     * @param sort_order
     *   The order to sort in 'desc' or 'asc'.
     */
    $scope.setSort = function(sort_field, sort_order) {
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
