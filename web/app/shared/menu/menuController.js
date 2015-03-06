/**
 * @file
 * Contains the controller for the menues.
 */

/**
 * Menu controller. Controls the menues.
 */
angular.module('ikApp').controller('MenuController', ['$scope', '$rootScope', '$location', 'userFactory', 'configuration',
  function ($scope, $rootScope, $location, userFactory, configuration) {
    'use strict';

    $scope.url = $location.url();
    $scope.navMenuOpen = null;
    $scope.subMenuItems = [];
    $scope.showMobileMainMenu = false;
    $scope.showSharingOptions = configuration.sharingService.enabled;
    $scope.siteTitle = configuration.siteTitle;

    userFactory.getCurrentUser().then(
      function (data) {
        $scope.currentUser = data;
      }
    );

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

    /**
     * Setup listener for when the url changes.
     */
    $rootScope.$on('$locationChangeSuccess', function () {
      $scope.url = $location.url();
      $scope.navMenuOpen = false;
      updateSubMenu();
    });

    /**
     * Show/hide mobile main menu.
     */
    $scope.mobileMainMenuVisible = function () {
      return $scope.url.indexOf('/channel') == 0 && $scope.url.indexOf('/slide') == 0 && $scope.url.indexOf('/screen') == 0 && $scope.url.indexOf('/media') == 0;
    }
  }
]);


