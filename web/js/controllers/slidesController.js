ikApp.controller('SlidesController', function($scope, slideFactory) {
  slideFactory.getSlides().then(function(data) {
    $scope.slides = data;
  });

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