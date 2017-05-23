/**
 * @file
 * Controllers for media edit
 */

/**
 * Media controller. Controls media editing functions.
 */
angular.module('ikApp').controller('MediaEditController', ['$scope', '$location', '$routeParams', '$timeout', 'mediaFactory', 'busService', '$controller',
  function ($scope, $location, $routeParams, $timeout, mediaFactory, busService, $controller) {
    'use strict';

    // Extend BaseController.
    $controller('BaseController', {$scope: $scope});

    // Get the selected media
    mediaFactory.getMedia($routeParams.id).then(
      function success(data) {
        // Check permission.
        if (!$scope.baseCanUpdate(data)) {
          busService.$emit('log.error', {
            timeout: 5000,
            cause: 403,
            msg: 'Du har ikke ret til ændre i dette indhold'
          });

          $location.path('/');
          return;
        }

        $scope.media = data;

        if ($scope.media === {}) {
          $location.path('/media-overview');
        }
      },
      function error(reason) {
        busService.$emit('log.error', {
          'cause': reason,
          'msg': 'Kunne ikke hente media med id: ' + $routeParams.id
        });
        $location.path('/media-overview');
      }
    );

    /**
     * Delete an image.
     */
    $scope.delete = function () {
      mediaFactory.deleteMedia($scope.media.id).then(
        function success() {
          busService.$emit('log.info', {
            'msg': 'Media slettet.',
            'timeout': 3000
          });
          $timeout(function () {
            $location.path('/media-overview');
          }, 500);
        },
        function error(reason) {
          busService.$emit('log.error', {
            'cause': reason,
            'msg': 'Sletning af media fejlede.'
          });
        }
      );
    };

    /**
     * Get the content type of a media: image or media
     * @param media
     * @returns {*}
     */
    $scope.getContentType = function (media) {
      if (!media) {
        return "";
      }

      var type = media.content_type.split("/");
      return type[0];
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
    $scope.videoPath = function (element, format) {
      // Init the filepath.
      var filepath = '';

      // Loop through the different video formats.
      element.provider_metadata.forEach(function (entry) {

        // Compare video format to desired format.
        if (entry.format === format) {
          filepath = entry.reference;
        }

        // Use thumbnail image.
        if (format === 'thumbnail_landscape') {
          // Use thumbnail from mp4 landscape.
          entry.thumbnails.forEach(function (thumbnail) {
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