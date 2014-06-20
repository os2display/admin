ikApp.controller('MenuController', function($scope, $location, menuFactory) {
  menuFactory.setLocation($location);
  $scope.menu = menuFactory.getMenuItems();
  $scope.menuOpen = null;


  // Navigation menu open / close
  $scope.toggleMenu = function(){
    if ($scope.menuOpen === null) {
      $scope.menuOpen = false;
    }
    $scope.menuOpen = !$scope.menuOpen;
    console.log('123');
  };
});
