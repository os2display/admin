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
    $scope.orientation = 'all';
    $scope.sort = { "created_at": "desc" };


    // Slides to display.
    $scope.slides = [];

    // Setup default search options.
    var search = {
      "fields": [ 'title' ],
      "text": $scope.search_text,
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
          // Extract search ids.
          var ids = [];
          for (var i = 0; i < data.length; i++) {
            ids.push(data[i].id);
          }

          // Load slides bulk.
          slideFactory.loadSlidesBulk(ids).then(
            function(data) {
              $scope.slides = data;
            }
          );
        }
      );
    };

    /**
     * Changes orientation and updated the slides.
     *
     * @param orientation
     *   This should either be 'landscape' or 'portrait'.
     */
    $scope.setOrientation = function(orientation) {
      if ($scope.orientation !== orientation) {
        $scope.orientation = orientation;

        // Update search query.

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
      }
    };

    /**
     * Calculates if a scheduling is set and whether we are currently showing it or not.
     *
     * @param slide
     *   The current slide.
     *
     * @return
     *   True if the slide has a schedule set, and we are outside the scope of the schedule.
     */
    $scope.outOfSchedule = function outOfSchedule(slide) {
      if (slide.schedule_from && slide.schedule_to) { // From and to time is set.
        if (slide.schedule_from * 1000 < Date.now() && slide.schedule_to * 1000 > Date.now() ) {
          // Current time is between from and to time (ie inside schedule).
          return false;
        }
        // Current time is set but is outside from and to time (ie out of schedule).
        return true;
      }
      // No schedule is set.
      return false;
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

    // Send the default search query.
    $scope.updateSearch();
  }
]);
