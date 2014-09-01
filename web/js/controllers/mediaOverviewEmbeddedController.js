/**
 * @file
 * Media overview embedded controllers.
 */

/**
 * Media Overview Embedded Controller.
 *
 * Emits the 'mediaOverview.selectImage' event for a parent controller to catch.
 *   Catch this event to handle clicks on an image in the overview.
 */
ikApp.controller('MediaOverviewEmbeddedController', function ($scope, $http, $location, mediaFactory) {
  // Media to display.
  $scope.images = [];

  // Setup default search options.
  $scope.search = {
    "fields": 'name',
    "text": '',
    "sort": {
      "created_at" : {
        "order": "desc"
      }
    }
  };

  /**
   * Updates the images array by sending a search request.
   */
  $scope.updateSearch = function() {
    mediaFactory.searchMedia($scope.search).then(
      function(data) {
        $scope.images = data;
        console.log($scope.images);

        angular.forEach($scope.images, function(image, key) {
          image.url = image.urls.default_landscape;
        });
      }
    );
  };

  // Send the default search query.
  $scope.updateSearch();

  /**
   * Emits event when the user clicks an image.
   * @param image
   */
  $scope.mediaOverviewClickImage = function mediaOverviewClickImage(image) {
    $scope.$emit('mediaOverview.selectImage', image);
  }

  /**
   * Changes the sort order and updated the images.
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
});
