ikApp.controller('ScreensController', function($scope, screenFactory) {
  screenFactory.getScreens().then(function(data) {
    $scope.screens = data;
  });

  $scope.sort = '-created';
  $scope.search = {
    title: '',
    orientation: 'landscape'
  }

  $scope.setOrientation = function(orientation) {
    $scope.search.orientation = orientation;
    console.log($scope.screens);
  };

  $scope.setSort = function(sort) {
    $scope.sort = sort;
  };
});
