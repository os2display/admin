ikApp.controller('MenuController', function($scope, menuFactory) {
  $scope.menu = menuFactory.getMenuItems();
});
