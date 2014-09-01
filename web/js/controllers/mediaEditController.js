/**
 * Media controller. Controls media editing functions.
 */
ikApp.controller('MediaEditController', function($scope, $location, $routeParams, mediaFactory) {
  /**
   * Scope setup
   */

  mediaFactory.getImage($routeParams.id).then(function(data) {
    $scope.image = data;

    if ($scope.image === {}) {
      $location.path('/media-overview');
    }
  });

  $scope.delete = function(id) {
    mediaFactory.deleteImage($scope.image.id);
    $location.path('/media-overview');
  };


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
});