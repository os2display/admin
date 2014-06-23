ikApp.controller('MenuController', function($scope, $rootScope, $location) {
  $scope.url = $location.url();
  $scope.navMenuOpen = null;
  $scope.subMenuItems = [];

  var updateSubMenu = function() {
    if ($scope.url.indexOf('/channel') == 0) {
      $scope.subMenuItems = [
        {
          title: 'Oversigt',
          path: '/#/channels',
          classSuffix: 'overview'
        },
        {
          title: 'Opret kanal',
          path: '/#/channel',
          classSuffix: 'create-channel'
        }
      ];
    }
    else if ($scope.url.indexOf('/slide') == 0) {
      $scope.subMenuItems = [
        {
          title: 'Oversigt',
          path: '/#/slides',
          classSuffix: 'overview'
        },
        {
          title: 'Opret slide',
          path: '/#/slide',
          classSuffix: 'create-channel'
        }
      ];
    }
    else if ($scope.url.indexOf('/screen') == 0) {
      $scope.subMenuItems = [
        {
          title: 'Oversigt',
          path: '/#/screens',
          classSuffix: 'overview'
        },
        {
          title: 'Opret skærm',
          path: '/#/screen',
          classSuffix: 'create-channel'
        }
      ];
    }
    else if ($scope.url.indexOf('/template') == 0) {
      $scope.subMenuItems = [
        {
          title: 'Oversigt',
          path: '/#/templates',
          classSuffix: 'overview'
        },
        {
          title: 'Opret skabelon',
          path: '/#/template',
          classSuffix: 'create-channel'
        }
      ];
    }
    else {
      $scope.subMenuItems = [];
    }
  }
  updateSubMenu();

  // Navigation menu open / close
  $scope.toggleNavMenu = function(){
    if ($scope.navMenuOpen === null) {
      $scope.navMenuOpen = false;
    }
    $scope.navMenuOpen = !$scope.navMenuOpen;
  };

  $rootScope.$on('$locationChangeSuccess', function(){
    $scope.url = $location.url();
    $scope.navMenuOpen = false;
    updateSubMenu();
  });
});
