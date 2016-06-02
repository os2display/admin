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
      controller: 'TimelineScreenController',
      templateUrl: 'apps/timelineApp/screen-timeline.html?' + window.config.version
    })
    .when('/channel-timeline', {
      controller: 'TimelineChannelController',
      templateUrl: 'apps/timelineApp/channel-timeline.html?' + window.config.version
    });
});

// Setup the app.
//  - submenu items.
// @REVIEW: Should this be location in a service file.
// @REVIEW: Are we always sure that menuApp is loaded later or before this to
//          ensure messages on the bus. Should the bus have a buffer if no one
//          is listing?
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
                icon: 'event-note',
                weight: 4,
                activeFilter: '/screen-timeline'
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