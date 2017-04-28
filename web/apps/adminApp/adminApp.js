/**
 * @file
 * Sets up the Admin App.
 */

// Configure routing
angular.module('adminApp').config(function ($routeProvider) {
  'use strict';

  // Register routes
  $routeProvider
  .when('/admin', {
    controller: 'AdminPageController',
    templateUrl: 'apps/adminApp/admin-page.html?' + window.config.version
  });
});

// Setup the app.
//  - submenu items.
// @REVIEW: Should this be location in a service file.
// @REVIEW: Are we always sure that menuApp is loaded later or before this to
//          ensure messages on the bus. Should the bus have a buffer if no one
//          is listing?
angular.module('adminApp').service('adminAppSetup', [
  'busService',
  function (busService) {
    'use strict';

    // Register listener for requests for Main Menu items
    busService.$on('menuApp.requestMainMenuItems', function requestMainMenuItems(event, args) {
      busService.$emit('menuApp.returnMainMenuItems', [
        {
          title: "Admin",
          route: '/#/admin',
          activeFilter: '/admin',
          icon: 'picture_in_picture',
          weight: 5
        }
      ]);
    });

    // Listen for sub menu requests
    busService.$on('menuApp.requestSubMenuItems', function (event, data) {
      busService.$emit('menuApp.returnSubMenuItems', [
          {
            mainMenuItem: 'admin',
            items: [
              {
                title: 'Oversigt',
                path: '#/admin',
                classes: 'overview-right',
                activeFilter: '/admin',
                group: 'left',
                weight: 1
              },
              {
                title: 'Brugere',
                path: '#/admin/users',
                classes: 'admin-users',
                activeFilter: '/admin/user',
                group: 'left',
                weight: 2
              },
              {
                title: 'Grupper',
                path: '#/admin/groups',
                classes: 'overview-right',
                activeFilter: '/admin/group',
                group: 'left',
                weight: 3
              }
            ]
          }
        ]
      )
    });
  }
]);

// Start the service.
angular.module('adminApp').run(['adminAppSetup', angular.noop]);