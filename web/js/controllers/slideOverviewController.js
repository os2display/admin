/**
 * @file
 * Slide overview controllers.
 */

/**
 * Slide overview controller handles the display and selection of slides.
 */
ikApp.controller('SlideOverviewController', ['$scope', 'slideFactory',
  function($scope, slideFactory) {
    // Set default orientation and sort.
    $scope.orientation = 'landscape';
    $scope.sort = { "created_at": "desc" };


    // Slides to display.
    $scope.slides = [];

    // Setup default search options.
    var search = {
      "fields": [ 'title' ],
      "text": $scope.search_text,
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
     * Updates the slides array by send a search request.
     */
    $scope.updateSearch = function() {
      // Get search text from scope.
      search.text = $scope.search_text;

      slideFactory.searchSlides(search).then(
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
      $scope.orientation = orientation;

      // Update search query.
      search.filter.bool.must.term.orientation = $scope.orientation;

      $scope.updateSearch();
    };

    /**
     * Changes the sort order and updated the slides.
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
