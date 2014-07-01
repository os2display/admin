ikApp.controller('ScreenGroupsController', function($scope, screenFactory) {
  screenFactory.getScreenGroups().then(function(data) {
    $scope.screenGroups = data;
  });

  $scope.sort = '-created';
  $scope.search = {
    title: ''
  }

  $scope.setSort = function(sort) {
    $scope.sort = sort;
  };
});
