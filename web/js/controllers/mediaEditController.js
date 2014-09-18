/**
 * @file
 * Controllers for media edit
 */

/**
 * Media controller. Controls media editing functions.
 */
ikApp.controller('MediaEditController', ['$scope', '$location', '$routeParams', '$timeout', 'mediaFactory',
  function($scope, $location, $routeParams, $timeout, mediaFactory) {

    // Get the selected media
    mediaFactory.getMedia($routeParams.id).then(function(data) {
      $scope.media = data;

      if ($scope.media === {}) {
        $location.path('/media-overview');
      }
    });

    /**
     * Delete an image.
     * @param id
     */
    $scope.delete = function() {
      mediaFactory.deleteMedia($scope.media.id).then(function() {
        $timeout(function() {
          $location.path('/media-overview');
        }, 500);
      });
    };

    /**
     * Get the content type of a media: image or media
     * @param media
     * @returns {*}
     */
    $scope.getContentType = function(media) {
      if (!media) {
        return "";
      }

      var type = media.content_type.split("/");
      return type[0];
    }

    /**
     * Sets the correct local path to the video
     *
     * @param element
     * The media element.
     *
     * @param format
     * The desired format to display (ogv, mpeg or thumbnail_SIZE).
     */
    $scope.videoPath = function(element, format) {
      // Init the filepath.
      var filepath = '';

      // Loop through the different video formats.
      element.provider_metadata.forEach(function(entry) {

        // Compare video format to desired format.
        if (entry.format === format) {
          filepath = entry.reference;
        }

        // Use thumbnail image.
        if (format === 'thumbnail_landscape') {
          // Use thumbnail from mp4 landscape.
          entry.thumbnails.forEach(function(thumbnail) {
            if (thumbnail.label === 'mp4_landscape') {
              filepath = thumbnail.reference;
            }
          });
        }
      });
      return filepath;
    }
  }
]);