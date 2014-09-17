/**
 * @file
 * Contains directives for media.
 */

/**
 * Media Overview Directive.
 *
 * Directive to insert a media overview.
 *
 * Emits the mediaOverview.selectMedia event when a media from the overview has been clicked.
 *
 * @param media-type
 *   which media type should be shown, "image" or "video",
 *   leave out show all media.
 */
ikApp.directive('ikMediaOverview', function() {
  return {
    restrict: 'E',
    scope: {
      mediaType: '@',
      autoSearch: '@'
    },
    controller: function($scope, mediaFactory) {
      // Set default orientation and sort.
      $scope.sort = { "created_at": "desc" };

      // Set default search text.
      $scope.search_text = '';

      // Set default media type.
      $scope.media_type = '';

      // Media to display.
      $scope.media = [];

      // Setup default search options.
      var search = {
        "fields": 'name',
        "text": '',
        "sort": {
          "created_at" : {
            "order": "desc"
          }
        }
      };

      // Mouse hover on image.
      $scope.hovering = false;

      /**
       * Adds hover overlay on media elements.
       */
      $scope.mouseHover = function mouseHover(state) {
        if(state > 0) {
          $scope.hovering = state;
        } else {
          $scope.hovering = false;
        }
      };

      /**
       * Updates the images array by sending a search request.
       */
      $scope.updateSearch = function updateSearch() {
        // Get search text from scope.
        search.text = $scope.search_text;

        mediaFactory.searchMedia(search).then(
          function(data) {
            $scope.media = data;
          }
        );
      };

      // Send the default search query.
      if ($scope.autoSearch) {
        $scope.updateSearch();
      }

      /**
       * Set the media type to filter on.
       * @param type
       */
      $scope.filterMediaType = function filterMediaType(type) {
        // Only update search if value changes.
        if ($scope.media_type !== type) {
          // Update scope to show selection in GUI.
          $scope.media_type = type;

          // Remove filter if no type is set.
          if (type === '') {
            search.filter = undefined;
          }
          else {
            // Filter based on content type.
            search.filter = {
              "bool": {
                "must": {
                  "term": {
                    "content_type":  type
                  }
                }
              }
            };
          }

          // Update the search result.
          $scope.updateSearch();
        }
      };

      /**
       * Get the content type of a media: image or media
       *
       * @param mediaElement
       *
       * @returns "", "video" or "image"
       */
      $scope.getMediaType = function getMediaType(mediaElement) {
        if (!mediaElement) {
          return "";
        }

        var type = mediaElement.content_type.split("/");
        return type[0];
      };

      /**
       * Emits event when the user clicks a media.
       *
       * @param mediaElement
       */
      $scope.mediaOverviewClickMedia = function mediaOverviewClickImage(mediaElement) {
        $scope.$emit('mediaOverview.selectMedia', mediaElement);
      };

      /**
       * Handle mediaOverview.updateSearch events.
       */
      $scope.$on('mediaOverview.updateSearch', function(event) {
        $scope.updateSearch();

        event.preventDefault();
      });

      /**
       * Changes the sort order and updated the images.
       *
       * @param sort_field
       *   Field to sort on.
       * @param sort_order
       *   The order to sort in 'desc' or 'asc'.
       */
      $scope.setSort = function setSort(sort_field, sort_order) {
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
    },
    link: function(scope, element, attrs) {
      attrs.$observe('mediaType', function(val) {
        if (!val) {
          return;
        }
        if (val == scope.media_type) {
          return;
        }

        scope.filterMediaType(val);
      })
    },
    templateUrl: 'partials/directives/media-overview.html'
  };
});

/**
 * Media Upload Directive.
 *
 * Emits the mediaUpload.uploadSuccess event when upload i successful.
 *
 * Emits the 'mediaUpload.uploadComplete' event for a parent controller to catch.
 *   Catch this event to handle when the upload is complete.
 */
ikApp.directive('ikMediaUpload', function() {
  return {
    restrict: 'E',
    scope: {},
    controller: function($scope, FileUploader) {
      $scope.currentStep = 1;
      $scope.uploadComplete = false;
      $scope.uploadErrors = false;
      $scope.uploadErrorText = '';

      // Create an uploader
      $scope.uploader = new FileUploader({
        url: '/api/media',
        filters: [{
          name: 'imageFilter',
          fn: function(item /*{File|FileLikeObject}*/, options) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|mp4|'.indexOf(type) !== -1;
          }
        }]
      });

      /**
       * Calls the hidden select files button.
       */
      $scope.selectFiles = function() {
        angular.element( document.querySelector( '#select-files' )).click();
      };

      /**
       * Clear the uploader queue.
       */
      $scope.clearQueue = function() {
        $scope.uploader.clearQueue();
        $scope.uploadComplete = false;
        $scope.uploadErrors = false;
        $scope.currentStep = 1;
      }

      /**
       * Remove item from the uploader queue.
       * @param item
       */
      $scope.removeItem = function(item) {
        item.remove();
        if ($scope.uploader.queue.length <= 0) {
          $scope.currentStep = 1;
          $scope.uploadComplete = false;
          $scope.uploadErrors = false;
        }
      }

      /**
       * Checks whether the item is an image.
       */
      $scope.isImage = function(item) {
        var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
        return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
      };

      /**
       * After adding a file to the upload queue, add an empty title to the file item.
       */
      $scope.uploader.onAfterAddingFile = function(item) {
        item.formData = [{title: ''}];
      };

      /**
       * After adding all files, increase current step.
       * @param item
       */
      $scope.uploader.onAfterAddingAll = function() {
        $scope.currentStep++;
      };

      /**
       * If an error occurs.
       * @param item
       * @param response
       * @param status
       * @param headers
       */
      $scope.uploader.onErrorItem = function(item, response, status, headers) {
        $scope.uploadErrors = true;

        if (status === 413) {
          $scope.uploadErrorText = "Billedet var for stort (fejlkode: 413)";
        } else {
          $scope.uploadErrorText = "Der skete en fejl (fejlkode: " + status + ")";
        }
      };

      /**
       * When all uploads are complete.
       */
      $scope.uploader.onCompleteAll = function() {
        $scope.uploadComplete = true;
      };

      /**
       * When an item has been uploaded successfully.
       * @param item
       * @param response
       * @param status
       * @param headers
       */
      $scope.uploader.onSuccessItem = function(item, response, status, headers) {
        $scope.$emit('mediaUpload.uploadSuccess', {
          image: item,
          id: response[0],
          queue: $scope.uploader.queue
        });
      };
    },
    link: function(scope, element, attrs) {
    },
    templateUrl: 'partials/directives/media-upload.html'
  };
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

      if (!helper.isFile(params.file)) {
        console.log("ikThumb: Not a file");
        return;
      }
      if (!helper.isImage(params.file)) {
        console.log("ikThumb: Not an image");
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
