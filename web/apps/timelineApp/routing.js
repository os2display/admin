/**
 * @file
 * Sets up the Timeline App.
 */

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
