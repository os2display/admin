/**
 * @file
 * Screens controller handles the display and selection of screens.
 */

ikApp.controller('ScreensController', function($scope, screenFactory) {
  $scope.screens = [];

  // Setup default search options.
  $scope.search = {
    "fields": 'title',
    "text": '',
    "filter": {
      "orientation":  'landscape'
    },
    "sort": {
      "created" : {
        "order": "desc"
      }
    }
  };

  /**
   * Updates the screens array by send a search request.
   */
  var updateScreens = function() {
    screenFactory.searchScreens($scope.search).then(
      function(data) {
        $scope.screens = data;
      }
    );
  };

  // Send the default search query.
  updateScreens();

  /**
   * Changes orientation and updated the screens.
   *
   * @param orientation
   *   This should either be 'landscape' or 'portrait'.
   */
  $scope.setOrientation = function(orientation) {
    $scope.search.filter.orientation = orientation;

    updateScreens();
  };

  /**
   * Changes the sort order and updated the screens.
   *
   * @param sort
   *   Field to sort on.
   * @param sortOrder
   *   The order to sort in 'desc' or 'asc'.
   */
  $scope.setSort = function(sort, sortOrder) {
    $scope.search.sort = {};
    $scope.search.sort[sort] = {
      "order": sortOrder
    };

    updateScreens();
  };

  // Hook into the search field.
  $('.js-text-field').off("keyup").on("keyup", function() {
    if (event.keyCode === 13 || $scope.search.text.length >= 3) {
      updateScreens();
    }
  });
});
