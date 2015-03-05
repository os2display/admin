/**
 * @file
 * Contains controllers for the media overview page.
 */

/**
 * Media controller handles the media overview page.
 */
angular.module('ikApp').controller('MediaOverviewController', ['$scope', '$location',
  function ($scope, $location) {
    'use strict';

    // Register event listener for the select media event.
    $scope.$on('mediaOverview.selectMedia', function(event, media) {
      $location.path('/media/' + media.id);
    });
  }
]);
