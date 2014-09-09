/**
 * @file
 * Contains directives for media.
 */

/**
 * Directive to insert a media overview.
 * @param media-type
 *   which media type should be shown, "image" or "video",
 *   leave out show all media.
 */
ikApp.directive('ikMediaOverview', function() {
  return {
    restrict: 'E',
    scope: {
      mediaType: '@'
    },
    controller: function($scope, $http, $location, mediaFactory) {
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

            angular.forEach($scope.images, function(image, key) {
              image.url = image.urls.default_landscape;
            });
          }
        );
      };

      // Send the default search query.
      $scope.updateSearch();

      /**
       * Get the content type of a media: image or media
       * @param media
       * @returns "", "video" or "image"
       */
      $scope.getContentType = function(media) {
        if (!media) {
          return "";
        }

        var type = media.content_type.split("/");
        return type[0];
      }

      /**
       * Emits event when the user clicks a media.
       * @param media
       */
      $scope.mediaOverviewClickMedia = function mediaOverviewClickImage(media) {
        $scope.$emit('mediaOverview.selectMedia', media);
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
    },
    link: function(scope, element, attrs) {
    },
    templateUrl: 'partials/directives/media-overview.html'
  }
});

/**
 * The ng-thumb directive
 * NB! Has been renamed to ik-thumb
 * @author: nerv
 * @version: 0.1.2, 2014-01-09
 */
ikApp.directive('ikThumb', ['$window', function($window) {
  var helper = {
    support: !!($window.FileReader && $window.CanvasRenderingContext2D),
    isFile: function(item) {
      return angular.isObject(item) && item instanceof $window.File;
    },
    isImage: function(file) {
      var type =  '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
      return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
    }
  };

  return {
    restrict: 'A',
    template: '<canvas/>',
    link: function(scope, element, attributes) {
      if (!helper.support) return;

      var params = scope.$eval(attributes.ikThumb);
      console.log(params);

      if (!helper.isFile(params.file)) {
        console.log("Not a file");
        return;
      }
      if (!helper.isImage(params.file)) {
        console.log("Not an image");
        return;
      }

      var canvas = element.find('canvas');

      var reader = new FileReader();

      reader.onload = onLoadFile;
      reader.readAsDataURL(params.file);

      function onLoadFile(event) {
        var img = new Image();
        img.onload = onLoadImage;
        img.src = event.target.result;
      }

      function onLoadImage() {
        var width = params.width || this.width / this.height * params.height;
        var height = params.height || this.height / this.width * params.width;
        canvas.attr({ width: width, height: height });
        canvas[0].getContext('2d').drawImage(this, 0, 0, width, height);
      }
    }
  };
}]);