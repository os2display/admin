/**
 * Slide edit controller. Controls the slide creation process.
 */
ikApp.controller('SlideEditController', function($scope, $http, mediaFactory, slideFactory) {
  // Get the slide from the backend.
  slideFactory.getEditSlide(null).then(function(data) {
    $scope.slide = data;
  });

  $scope.step = 'background-picker';

  $scope.pickFromMedia = function pickFromMedia() {
    updateImages();

    $scope.step = 'pick-from-media';
  };

  $scope.pickFromComputer = function pickFromComputer() {
    $scope.step = 'pick-from-computer';
  };

  // Setup some default configuration.
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

  $scope.mediaOverviewClickImage = function clickImage(image) {
    $scope.slide.options.image = image.url;

    $scope.editor.showBackgroundEditor = false;
    $scope.editor.showTextEditor = false;
  }

  // Setup editor states and functions.
  $scope.editor = {
    showTextEditor: false,
    toggleTextEditor: function() {
      $scope.editor.showBackgroundEditor = false;
      $scope.editor.showTextEditor = !$scope.editor.showTextEditor;
    },
    showBackgroundEditor: false,
    toggleBackgroundEditor: function() {
      $scope.editor.showTextEditor = false;
      $scope.editor.showBackgroundEditor = !$scope.editor.showBackgroundEditor;
    }
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
    $scope.search.sort = {};
    $scope.search.sort[sort] = {
      "order": sortOrder
    };

    updateImages();
  };

  // Hook into the search field.
  $('.js-text-field').off("keyup").on("keyup", function() {
    if (event.keyCode === 13 || $scope.search.text.length >= 3) {
      updateImages();
    }
  });
});