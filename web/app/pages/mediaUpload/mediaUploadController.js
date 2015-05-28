/**
 * @file
 * Handles the media upload page
 */

/**
 * Media upload controller. Controls the media upload page.
 */
angular.module('ikApp').controller('MediaUploadController', ['$scope',
  function ($scope) {
    'use strict';

    // Register event listener for uploadComplete action.
    $scope.$on('mediaUpload.uploadComplete', function(event, data) {

    });
  }
]);
