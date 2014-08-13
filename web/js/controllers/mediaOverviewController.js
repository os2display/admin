/**
 * @file
 * Images controller handles the display, selection and upload of image.
 */

ikApp.controller('MediaOverviewController', function ($scope, $http, mediaFactory) {
  // Setup some default configuration.
  $scope.images = [];

  // Setup default search options.
  $scope.search = {
    "fields": 'title',
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
   * Changes the sort order and updated the images.
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

    updateImages();
  };

  // Send the default search query.
  updateImages();

  // Hook into the search field.
  $('.js-text-field').off("keyup").on("keyup", function() {
    if (event.keyCode === 13 || $scope.search.text.length >= 3) {
      updateImages();
    }
  });
});
