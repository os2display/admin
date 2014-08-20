/**
 * Media Overview Embedded Controller.
 *
 * Emits the 'mediaOverview.selectImage' event for a parent controller to catch.
 *   Catch this event to handle clicks on an image in the overview.
 */
ikApp.controller('MediaOverviewEmbeddedController', function ($scope, $http, $location, mediaFactory) {
  // Setup some default configuration.
  $scope.images = [];

  // Setup default search options.
  $scope.search = {
    "fields": 'name',
    "text": '',
    "sort": [{
      "created_at.raw" : {
        "order": "desc"
      }
    }]
  };

  /**
   * Updates the images array by sending a search request.
   */
  var updateImages = function() {
    mediaFactory.searchMedia($scope.search).then(
      function(data) {
        $scope.images = data;

        angular.forEach($scope.images, function(value, key) {
          $http.get('/api/media/' + value.id)
            .success(function(data, status) {
              $scope.images[key].url = data.urls.landscape;
            })
        });
      }
    );
  };

  /**
   * Perform search
   */
  $scope.updateSearch = function() {
    updateImages();
  }

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
   * @param sort
   *   Field to sort on.
   * @param sortOrder
   *   The order to sort in 'desc' or 'asc'.
   */
  $scope.setSort = function(sort, sortOrder) {
    sort += ".raw";
    $scope.search.sort = {};
    $scope.search.sort[sort] = {
      "order": sortOrder
    };

    updateImages();
  };

  // Send the default search query.
  updateImages();

});
