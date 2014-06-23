ikApp.controller('MenuController', function($scope, $rootScope, $location) {
  $scope.selectedMenuItem = '';
  $scope.navMenuOpen = null;

  // Navigation menu open / close
  $scope.toggleNavMenu = function(){
    if ($scope.navMenuOpen === null) {
      $scope.navMenuOpen = false;
    }
    $scope.navMenuOpen = !$scope.navMenuOpen;
  };

  $rootScope.$on('$locationChangeSuccess', function(){
    $scope.url = $location.url();
  });
});
