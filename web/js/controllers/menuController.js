ikApp.controller('MenuController', function($scope, $rootScope, $location) {
  $scope.url = $location.url();
  $scope.navMenuOpen = null;
  $scope.subMenuItems = [];
  $scope.showMobileMainMenu = false;

  /**
   * Set the submenu items according to what the url starts with.
   */
  var updateSubMenu = function() {
    if ($scope.url.indexOf('/channel') == 0) {
      $scope.subMenuItems = [
        {
          title: 'Oversigt',
          path: 'channels',
          classSuffix: 'overview'
        },
        {
          title: 'Opret kanal',
          path: 'channel',
          classSuffix: 'create-channel'
        }
      ];
    }
    else if ($scope.url.indexOf('/slide') == 0) {
      $scope.subMenuItems = [
        {
          title: 'Oversigt',
          path: 'slides',
          classSuffix: 'overview'
        },
        {
          title: 'Opret slide',
          path: 'slide',
          classSuffix: 'create-channel'
        }
      ];
    }
    else if ($scope.url.indexOf('/screen') == 0) {
      $scope.subMenuItems = [
        {
          title: 'Oversigt',
          path: 'screens',
          classSuffix: 'overview'
        },
        {
          title: 'Opret skærm',
          path: 'screen',
          classSuffix: 'create-channel'
        }
      ];
    }
    else if ($scope.url.indexOf('/template') == 0) {
      $scope.subMenuItems = [
        {
          title: 'Oversigt',
          path: 'templates',
          classSuffix: 'overview'
        },
        {
          title: 'Opret skabelon',
          path: 'template',
          classSuffix: 'create-channel'
        }
      ];
    }
    else if ($scope.url.indexOf('/media') == 0) {
        $scope.subMenuItems = [
            {
                title: 'Oversigt',
                path: 'media',
                classSuffix: 'overview'
            },
            {
                title: 'Upload',
                path: 'media/upload',
                classSuffix: 'create-media'
            }
        ];
    }
    else {
      $scope.subMenuItems = [];
    }
  }
  updateSubMenu();

  /**
   * Function to see if the first part of a path matches the pattern.
   * @param str
   * @param pattern
   * @returns {boolean}
   */
  $scope.pathStartsWith = function(str, pattern) {
    var split = str.split('/');

    if (split.length >= 2) {
      str = "";
      for (var i = 1; i < split.length; i++) {
        str = str + split[i];
        if (i < split.length - 1) {
          str = str + "/";
        }
      }
    }

    if (str === pattern) {
      return true;
    }
    return false;
  }

  /**
   * Open/Close navigation menu.
   */
  $scope.toggleNavMenu = function(){
    if ($scope.navMenuOpen === null) {
      $scope.navMenuOpen = false;
    }
    $scope.navMenuOpen = !$scope.navMenuOpen;
    $('html').toggleClass('is-locked');
  };

  /**
   * Setup listener for when the url changes.
   */
  $rootScope.$on('$locationChangeSuccess', function(){
    $scope.url = $location.url();
    $scope.navMenuOpen = false;
    updateSubMenu();
  });

  /**
   * Show/hide mobile main menu.
   */
  $scope.mobileMainMenuVisible = function() {
    if($scope.url.indexOf('/channel') == 0 || $scope.url.indexOf('/slide') == 0 || $scope.url.indexOf('/screen') == 0 || $scope.url.indexOf('/template') == 0 || $scope.url.indexOf('/media') == 0) {
      return false;
    } else {
      return true;
    }
  }
});


