/**
 * @file
 * This is a TEMPORARY menu item provider.
 */

/**
 * MenuItemService.
 */
angular.module('mainApp').service('menuItemService', ['busService',
  function (busService) {
    'use strict';

    // Register listener for requests for Main Menu items
    busService.$on('menuApp.requestMainMenuItems', function requestMainMenuItems(event, args) {
      busService.$emit('menuApp.returnMainMenuItems', [
        {
          title: 'Kanaler',
          route: '/#/channel-overview',
          weight: 1
        },
        {
          title: 'Slides',
          route: '/#/slide-overview',
          weight: 2
        },
        {
          title: 'Skærme',
          route: '/#/screen-overview',
          weight: 3
        },
        {
          title: "Medier",
          route: '/#/media-overview',
          weight: 4
        }
      ]);
    })
  }
]);
