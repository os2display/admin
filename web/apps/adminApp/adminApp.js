/**
 * @file
 * Sets up the Admin App.
 */

// Configure routing and translations.
angular.module('adminApp').config(['$routeProvider', '$translateProvider', function ($routeProvider, $translateProvider) {
  'use strict';

  // Set up translations.
  $translateProvider
  .useSanitizeValueStrategy('escape')
  .useStaticFilesLoader({
    prefix: 'apps/adminApp/translations/locale-',
    suffix: '.json'
  })
  .preferredLanguage('da')
  .fallbackLanguage('da')
  .forceAsyncReload(true);

  // Register routes
  $routeProvider
  // Dashboard
  .when('/admin', {
    controller: 'AdminDashboardController',
    templateUrl: 'apps/adminApp/dashboard/admin-dashboard.html?' + window.config.version
  })
  // Users
  .when('/user', {
    controller: 'AdminUserController',
    templateUrl: 'apps/adminApp/user/admin-user.html?' + window.config.version
  })
  .when('/admin/users', {
    controller: 'AdminUsersController',
    templateUrl: 'apps/adminApp/user/admin-users.html?' + window.config.version
  })
  .when('/admin/user/:id', {
    controller: 'AdminUserController',
    templateUrl: 'apps/adminApp/user/admin-user.html?' + window.config.version
  })
  // Groups
  .when('/admin/groups', {
    controller: 'AdminGroupsController',
    templateUrl: 'apps/adminApp/group/admin-groups.html?' + window.config.version
  })
  .when('/admin/group/:id', {
    controller: 'AdminGroupController',
    templateUrl: 'apps/adminApp/group/admin-group.html?' + window.config.version
  })
  ;
}]);

// Setup the app.
//  - submenu items.
// @REVIEW: Should this be location in a service file.
// @REVIEW: Are we always sure that menuApp is loaded later or before this to
//          ensure messages on the bus. Should the bus have a buffer if no one
//          is listing?
angular.module('adminApp').service('adminAppSetup', [
  'busService', 'userService',
  function (busService, userService) {
    'use strict';

    // Register listener for requests for Main Menu items
    busService.$on('menuApp.requestMainMenuItems', function requestMainMenuItems(event, args) {
      var user = userService.getCurrentUser();

      if (user.is_admin) {
        busService.$emit('menuApp.returnMainMenuItems', [
          {
            title: "Admin",
            route: '/#/admin',
            activeFilter: '/admin',
            icon: 'picture_in_picture',
            weight: 5
          }
        ]);
      }
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
