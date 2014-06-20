ikApp.controller('MenuController', function($scope, $location, menuFactory) {
  menuFactory.setLocation($location);
  $scope.menu = menuFactory.getMenuItems();

  console.log($scope.menu);
});
