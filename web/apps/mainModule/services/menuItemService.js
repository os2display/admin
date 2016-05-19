/**
 * @file
 * This is a TEMPORARY menu item provider.
 * @TODO: Remove this, when the routes are provided by modules!
 */

/**
 * MenuItemService.
 */
angular.module('mainModule').service('menuItemService', ['busService',
  function (busService) {
    'use strict';

    // Listen for location change
    busService.$on('menuApp.requestSubMenuItems', function (event, data) {
      busService.$emit('menuApp.returnSubMenuItems', [
          {
            mainMenuItem: 'screen',
            items: [
              {
                title: 'Oversigt',
                path: '/#/screen-overview',
                classes: 'overview',
                group: 'left',
                weight: 1
              },
              {
                title: 'Opret skærm',
                path: '/#/screen',
                classes: 'create-channel',
                group: 'left',
                weight: 2
              },
              {
                title: 'Timeline',
                path: '/#/screen-timeline',
                classes: 'screen-timeline',
                group: 'right',
                weight: 3
              }
            ]
          },
          {
            mainMenuItem: 'channel',
            items: [
              {
                title: 'Oversigt',
                path: '/#/channel-overview',
                classSuffix: 'overview'
              },
              {
                title: 'Opret kanal',
                path: '/#/channel',
                classSuffix: 'create-channel'
              },
              {
                title: 'Delte kanaler',
                path: '/#/shared-channel-overview',
                classSuffix: 'overview'
              }
            ]
          },
          {
            mainMenuItem: 'slide',
            items: [
              {
                title: 'Oversigt',
                path: '/#/slide-overview',
                classSuffix: 'overview'
              },
              {
                title: 'Opret slide',
                path: '/#/slide',
                classSuffix: 'create-channel'
              }
            ]
          },
          {
            mainMenuItem: 'media',
            items: [
              {
                title: 'Oversigt',
                path: '/#/media-overview',
                classSuffix: 'overview'
              },
              {
                title: 'Upload medie',
                path: '/#/media/upload',
                classSuffix: 'create-media'
              }
            ]
          }
        ]
      );
    });

    // Register listener for requests for Main Menu items
    busService.$on('menuApp.requestMainMenuItems', function requestMainMenuItems(event, args) {
      busService.$emit('menuApp.returnMainMenuItems', [
        {
          title: 'Kanaler',
          route: '/#/channel-overview',
          activeFilter: '/channel',
          icon: 'add_to_queue',
          weight: 1
        },
        {
          title: 'Skærme',
          route: '/#/screen-overview',
          activeFilter: '/screen',
          icon: 'tv',
          weight: 3
        },
        {
          title: 'Slides',
          route: '/#/slide-overview',
          activeFilter: '/slide',
          icon: 'dvr',
          weight: 2
        },
        {
          title: "Medier",
          route: '/#/media-overview',
          activeFilter: '/media',
          icon: 'picture_in_picture',
          weight: 4
        }
      ]);
    });

    busService.$on('menuApp.requestHamburgerMenuItems', function requestHamburgerMenuItems(event, args) {
      busService.$emit('menuApp.returnHamburgerMenuItems', [
        {
          title: 'Kanaler',
          weight: 1,
          items: [
            {
              title: 'Oversigt',
              route: '/#/channel-overview',
              activeFilter: '/channel-overview',
              weight: 1
            },
            {
              title: 'Opret kanal',
              route: '/#/channel',
              activeFilter: '/channel',
              weight: 2
            }
          ]
        },
        {
          title: 'Slides',
          weight: 2,
          items: [
            {
              title: 'Oversigt',
              route: '/#/slide-overview',
              activeFilter: '/slide-overview',
              weight: 1
            },
            {
              title: 'Opret slide',
              route: '/#/slide',
              activeFilter: '/slide',
              weight: 2
            }
          ]
        },
        {
          title: 'Skærme',
          weight: 3,
          items: [
            {
              title: 'Oversigt',
              route: '/#/screen-overview',
              activeFilter: '/screen-overview',
              weight: 1
            },
            {
              title: 'Opret skærm',
              route: '/#/screen',
              activeFilter: '/screen',
              weight: 2
            }
          ]
        },
        {
          title: 'Administration',
          weight: 3,
          permission: 'super-admin',
          items: [
            {
              title: 'Brugere',
              route: '/admin',
              permission: 'super-admin',
              weight: 1
            },
            {
              title: 'Deling',
              route: '/#/admin-sharing',
              activeFilter: '/admin-sharing',
              permission: 'super-admin',
              weight: 2
            },
            {
              title: 'Skabeloner',
              route: '/#/admin-templates',
              activeFilter: '/admin-sharing',
              permission: 'super-admin',
              weight: 3
            }
          ]
        },
        {
          title: 'Information',
          weight: 4,
          items: [
            {
              title: 'Style guide',
              route: '/aroskanalen/style-guide',
              weight: 1
            }
          ]
        }
      ]);
    });
  }
]);
