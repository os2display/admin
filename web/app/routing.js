/**
 * @file
 * Contains the routing for the ikApp module.
 */

/**
 * Routing.
 */
angular.module('ikApp').config(function ($routeProvider) {
  'use strict';

  $routeProvider
    // Frontpage, set to channel overview.
    .when('/', {
      redirectTo: '/channel-overview'
    })

    .when('/admin-sharing', {
      controller: 'AdminSharingController',
      templateUrl: 'app/pages/adminSharing/admin-sharing.html?' + window.config.version
    })
    .when('/admin-templates', {
      controller: 'AdminTemplatesController',
      templateUrl: 'app/pages/adminTemplates/admin-templates.html?' + window.config.version
    })

    // Overviews
    .when('/channel-overview', {
      controller: 'ChannelOverviewController',
      templateUrl: 'app/pages/channelOverview/channel-overview.html?' + window.config.version
    })
    .when('/slide-overview', {
      controller: 'SlideOverviewController',
      templateUrl: 'app/pages/slideOverview/slide-overview.html?' + window.config.version
    })
    .when('/screen-overview', {
      controller: 'ScreenOverviewController',
      templateUrl: 'app/pages/screenOverview/screen-overview.html?' + window.config.version
    })
    .when('/media-overview', {
      controller: 'MediaOverviewController',
      templateUrl: 'app/pages/mediaOverview/media-overview.html?' + window.config.version
    })
    .when('/shared-channel-overview', {
      controller: 'SharedChannelOverviewController',
      templateUrl: 'app/pages/sharedChannelOverview/shared-channel-overview.html?' + window.config.version
    })

    // Screen
    .when('/screen', {
      controller: 'ScreenController',
      templateUrl: 'app/pages/screen/screen.html?' + window.config.version
    })
    .when('/screen/:id', {
      controller: 'ScreenController',
      templateUrl: 'app/pages/screen/screen.html?' + window.config.version
    })

    // Slide
    .when('/slide', {
      controller: 'SlideController',
      templateUrl: 'app/pages/slide/slide.html?' + window.config.version
    })
    .when('/slide/:id', {
      controller: 'SlideController',
      templateUrl: 'app/pages/slide/slide.html?' + window.config.version
    })

    // Channel
    .when('/shared-channel/:id/:index', {
      controller: 'SharedChannelController',
      templateUrl: 'app/pages/sharedChannel/shared-channel.html?' + window.config.version
    })
    .when('/channel', {
      controller: 'ChannelController',
      templateUrl: 'app/pages/channel/channel.html?' + window.config.version
    })
    .when('/channel/:id', {
      controller: 'ChannelController',
      templateUrl: 'app/pages/channel/channel.html?' + window.config.version
    })

    // Media
    .when('/media/upload', {
      controller: 'MediaUploadController',
      templateUrl: 'app/pages/mediaUpload/media-upload.html?' + window.config.version
    })
    .when('/media/:id', {
      controller: 'MediaEditController',
      templateUrl: 'app/pages/mediaEdit/media-edit.html?' + window.config.version
    })

    .otherwise({redirectTo: '/'});
});
