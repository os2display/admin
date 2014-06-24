ikApp.controller('OverviewController', function($scope, slideFactory) {
  $scope.slides = slideFactory.getSlides();
  $scope.sort = '-created';
  $scope.search = {
    title: '',
    orientation: 'landscape'
  }

  $scope.setOrientation = function(orientation) {
    $scope.search.orientation = orientation;
  };

  $scope.setSort = function(sort) {
    $scope.sort = sort;
  };
});