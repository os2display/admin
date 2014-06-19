ikApp.controller('SlidesController', function($scope, slideFactory) {
  $scope.slides = slideFactory.getSlides();
});