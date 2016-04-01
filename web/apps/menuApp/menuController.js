/**
 * @file
 * Contains the controller for the menues.
 */

/**
 * Menu controller. Controls the menues.
 */
angular.module('menuApp').controller('MenuController', ['$scope', '$rootScope', '$location', '$http', 'busService',
  function ($scope, $rootScope, $location, $http, busService) {
    'use strict';

    $scope.url = $location.url();
    $scope.navMenuOpen = null;
    $scope.subMenuItems = [];
    $scope.showMobileMainMenu = false;
    $scope.showSharingOptions = window.config.sharingService.enabled;
    $scope.siteTitle = window.config.siteTitle;
    $scope.mainMenuItems = [];
    $scope.hamburgerMenuItems = [];

    // Listen for Main menu items.
    busService.$on('mainApp.returnUser', function returnMainMenuItems(event, user) {
      $scope.currentUser = user;
    });

    // Request user
    busService.$emit('main.requestUser', {});

    // Listen for Main menu items.
    busService.$on('menuApp.returnMainMenuItems', function returnMainMenuItems(event, items) {
      // Add items received.
      items.forEach(function(element) {
        $scope.mainMenuItems.push(element);
      });

      // Sort by weight.
      $scope.mainMenuItems.sort(function(a, b) {
        return parseInt(a.weight) - parseInt(b.weight);
      });
    });

    // Request Main menu items
    busService.$emit('menuApp.requestMainMenuItems', {});

    // Listen for Hamburger menu items.
    busService.$on('menuApp.returnHamburgerMenuItems', function returnHamburgerMenuItems(event, items) {
      // Add items received.
      items.forEach(function(element) {
        $scope.hamburgerMenuItems.push(element);
      });

      // Sort by weight.
      $scope.hamburgerMenuItems.sort(function(a, b) {
        return parseInt(a.weight) - parseInt(b.weight);
      });
    });

    // Request Hamburger menu items
    busService.$emit('menuApp.requestHamburgerMenuItems', {});

    /**
     * Set the submenu items according to what the url starts with.
     */
    var updateSubMenu = function () {
      if ($scope.url.indexOf('/channel') === 0 || $scope.url.indexOf('/shared-channel') === 0) {
        $scope.subMenuItems = [
          {
            title: 'Oversigt',
            path: 'channel-overview',
            classSuffix: 'overview'
          },
          {
            title: 'Opret kanal',
            path: 'channel',
            classSuffix: 'create-channel'
          }
        ];

        if ($scope.showSharingOptions) {
          $scope.subMenuItems.push(
            {
              title: 'Delte kanaler',
              path: 'shared-channel-overview',
              classSuffix: 'overview'
            }
          );
        }
      }
      else if ($scope.url.indexOf('/slide') === 0) {
        $scope.subMenuItems = [
          {
            title: 'Oversigt',
            path: 'slide-overview',
            classSuffix: 'overview'
          },
          {
            title: 'Opret slide',
            path: 'slide',
            classSuffix: 'create-channel'
          }
        ];
      }
      else if ($scope.url.indexOf('/screen') === 0) {
        $scope.subMenuItems = [
          {
            title: 'Oversigt',
            path: 'screen-overview',
            classSuffix: 'overview'
          },
          {
            title: 'Opret skærm',
            path: 'screen',
            classSuffix: 'create-channel'
          }
        ];
      }
      else if ($scope.url.indexOf('/template') === 0) {
        $scope.subMenuItems = [
          {
            title: 'Oversigt',
            path: 'template-overview',
            classSuffix: 'overview'
          },
          {
            title: 'Opret skabelon',
            path: 'template',
            classSuffix: 'create-channel'
          }
        ];
      }
      else if ($scope.url.indexOf('/media') === 0) {
        $scope.subMenuItems = [
          {
            title: 'Oversigt',
            path: 'media-overview',
            classSuffix: 'overview'
          },
          {
            title: 'Upload medie',
            path: 'media/upload',
            classSuffix: 'create-media'
          }
        ];
      }
      else {
        $scope.subMenuItems = [];
      }
    };
    updateSubMenu();

    /**
     * Function to see if the first part of a path matches the pattern.
     * @param str
     * @param pattern
     * @returns {boolean}
     */
    $scope.pathStartsWith = function (str, pattern) {
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

      return str === pattern;
    };

    /**
     * Open/Close navigation menu.
     */
    $scope.toggleNavMenu = function () {
      if ($scope.navMenuOpen === null) {
        $scope.navMenuOpen = false;
      }
      $scope.navMenuOpen = !$scope.navMenuOpen;
      $('html').toggleClass('is-locked');
    };

    var closeNavMenu = function() {
      if ($scope.navMenuOpen === null) {
        $scope.navMenuOpen = false;
      }
      $scope.navMenuOpen = false;
      $('html').removeClass('is-locked');
    };

    /**
     * Setup listener for when the url changes.
     */
    $rootScope.$on('$locationChangeSuccess', function () {
      $scope.url = $location.url();
      $scope.navMenuOpen = false;
      $('html').removeClass('is-locked');
      updateSubMenu();

      busService.$emit('log.clear');
    });

    /**
     * Show/hide mobile main menu.
     */
    $scope.mobileMainMenuVisible = function () {
      return $scope.url.indexOf('/channel') == 0 && $scope.url.indexOf('/slide') == 0 && $scope.url.indexOf('/screen') == 0 && $scope.url.indexOf('/media') == 0;
    };

    /**
     * Update templates.
     */
    $scope.updateTemplates = function updateTemplates() {
      $http.get('/api/command/update_templates')
        .success(function(data, status, headers, config) {
          busService.$emit('log.info', {
            'msg': 'Templates opdateret.',
            'timeout': 3000
          });
        })
        .error(function(data, status, headers, config) {
          busService.$emit('log.error', {
            'cause': status,
            'msg': 'Update af templates fejlede.'
          });
        });
      closeNavMenu();
    };

    /**
     * Reindex search.
     */
    $scope.reindex = function reindex() {
      $http.get('/api/command/reindex')
        .success(function(data, status, headers, config) {
          busService.$emit('log.info', {
            'msg': 'Reindex gennemført.',
            'timeout': 3000
          });
        })
        .error(function(data, status, headers, config) {
          busService.$emit('log.error', {
            'cause': status,
            'msg': 'Reindex fejlede.'
          });
        });
      closeNavMenu();
    };

    /**
     * Force push.
     */
    $scope.forcePush = function reindex() {
      $http.get('/api/command/forcepush')
        .success(function(data, status, headers, config) {
          busService.$emit('log.info', {
            'msg': 'Force push gennemført.',
            'timeout': 3000
          });
        })
        .error(function(data, status, headers, config) {
          busService.$emit('log.error', {
            'cause': status,
            'msg': 'Force push fejlede.'
          });
        });
      closeNavMenu();
    };
  }
]);


