/**
 * @file
 * Sets up the Timeline App.
 */

// Configure routing
angular.module('timelineApp').config(function ($routeProvider) {
  'use strict';

  // Register routes
  $routeProvider
    .when('/screen-timeline', {
      templateUrl: 'apps/timelineApp/screen-timeline.html?' + window.config.version
    })
    .when('/channel-timeline', {
      templateUrl: 'apps/timelineApp/channel-timeline.html?' + window.config.version
    });
});

// Setup the app.
//  - submenu items.
angular.module('timelineApp').service('timelineAppSetup', ['busService',
  function (busService) {
    'use strict';

    // Listen for sub menu requests
    busService.$on('menuApp.requestSubMenuItems', function (event, data) {
      busService.$emit('menuApp.returnSubMenuItems', [
          {
            mainMenuItem: 'screen',
            items: [
              {
                title: 'Timeline',
                path: '/#/screen-timeline',
                classes: 'screen-timeline',
                group: 'right',
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
angular.module('timelineApp').run(['timelineAppSetup', angular.noop]);