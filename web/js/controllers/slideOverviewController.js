/**
 * @file
 * Slides controller handles the display and selection of slides.
 */
ikApp.controller('SlideOverviewController', function($scope, slideFactory) {
  $scope.slides = [];

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
   * Updates the slides array by send a search request.
   */
  var updateSlides = function() {
    slideFactory.searchSlides($scope.search).then(
      function(data) {
        $scope.slides = data;
      }
    );
  };

  // Send the default search query.
  updateSlides();

  /**
   * Changes orientation and updated the slides.
   *
   * @param orientation
   *   This should either be 'landscape' or 'portrait'.
   */
  $scope.setOrientation = function(orientation) {
    $scope.search.filter.orientation = orientation;

    updateSlides();
  };

  /**
   * Changes the sort order and updated the slides.
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

    updateSlides();
  };

  // Hook into the search field.
  $('.js-text-field').off("keyup").on("keyup", function() {
    if (event.keyCode === 13 || $scope.search.text.length >= 3) {
      updateSlides();
    }
  });
});
