/**
 * @file
 * Slide overview controllers.
 */

/**
 * Slide overview controller handles the display and selection of slides.
 */
angular.module('ikApp').controller('SlideOverviewController', ['$scope', '$location',
  function($scope, $location) {
    // Register event listener for the click slide event.
    $scope.$on('slideOverview.clickSlide', function(event, slide) {
      $location.path('/slide/' + slide.id);
    });
  }
]);
