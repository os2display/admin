/**
 * @file
 * Slide overview controllers.
 */

/**
 * Slide overview controller handles the display and selection of slides.
 */
ikApp.controller('SlideOverviewController', ['$scope', 'slideFactory',
  function($scope, slideFactory) {
    // Slides to display.
    $scope.slides = [];

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
     * Updates the slides array by send a search request.
     */
    $scope.updateSearch = function() {
      slideFactory.searchSlides($scope.search).then(
        function(data) {
          $scope.slides = data;
        }
      );
    };

    // Send the default search query.
    $scope.updateSearch();

    /**
     * Changes orientation and updated the slides.
     *
     * @param orientation
     *   This should either be 'landscape' or 'portrait'.
     */
    $scope.setOrientation = function(orientation) {
      $scope.search.filter.orientation = orientation;

      $scope.updateSearch();
    };

    /**
     * Changes the sort order and updated the slides.
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
