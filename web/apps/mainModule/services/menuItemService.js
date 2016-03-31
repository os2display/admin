/**
 * @file
 * This is a TEMPORARY menu item provider.
 */

/**
 * MenuItemService.
 */
angular.module('mainModule').service('menuItemService', ['busService',
  function (busService) {
    'use strict';

    // Register listener for requests for Main Menu items
    busService.$on('menuApp.requestMainMenuItems', function requestMainMenuItems(event, args) {
      busService.$emit('menuApp.returnMainMenuItems', [
        {
          title: 'Kanaler',
          route: '/channel-overview',
          activeFilter: '/channel',
          icon: 'add_to_queue',
          weight: 1
        },
        {
          title: 'Skærme',
          route: '/screen-overview',
          activeFilter: '/screen',
          icon: 'tv',
          weight: 3
        },
        {
          title: 'Slides',
          route: '/slide-overview',
          activeFilter: '/slide',
          icon: 'dvr',
          weight: 2
        },
        {
          title: "Medier",
          route: '/media-overview',
          activeFilter: '/media',
          icon: 'picture_in_picture',
          weight: 4
        }
      ]);
    })
  }
]);
