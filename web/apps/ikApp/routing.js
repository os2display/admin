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
      templateUrl: 'apps/ikApp/pages/adminSharing/admin-sharing.html?' + window.config.version
    })
    .when('/admin-templates', {
      controller: 'AdminTemplatesController',
      templateUrl: 'apps/ikApp/pages/adminTemplates/admin-templates.html?' + window.config.version
    })

    // Overviews
    .when('/channel-overview', {
      controller: 'ChannelOverviewController',
      templateUrl: 'apps/ikApp/pages/channelOverview/channel-overview.html?' + window.config.version
    })
    .when('/slide-overview', {
      controller: 'SlideOverviewController',
      templateUrl: 'apps/ikApp/pages/slideOverview/slide-overview.html?' + window.config.version
    })
    .when('/screen-overview', {
      controller: 'ScreenOverviewController',
      templateUrl: 'apps/ikApp/pages/screenOverview/screen-overview.html?' + window.config.version
    })
    .when('/media-overview', {
      controller: 'MediaOverviewController',
      templateUrl: 'apps/ikApp/pages/mediaOverview/media-overview.html?' + window.config.version
    })
    .when('/shared-channel-overview', {
      controller: 'SharedChannelOverviewController',
      templateUrl: 'apps/ikApp/pages/sharedChannelOverview/shared-channel-overview.html?' + window.config.version
    })

    // Screen
    .when('/screen', {
      controller: 'ScreenController',
      templateUrl: 'apps/ikApp/pages/screen/screen.html?' + window.config.version
    })
    .when('/screen/:id', {
      controller: 'ScreenController',
      templateUrl: 'apps/ikApp/pages/screen/screen.html?' + window.config.version
    })

    // Slide
    .when('/slide', {
      controller: 'SlideController',
      templateUrl: 'apps/ikApp/pages/slide/slide.html?' + window.config.version
    })
    .when('/slide/:id', {
      controller: 'SlideController',
      templateUrl: 'apps/ikApp/pages/slide/slide.html?' + window.config.version
    })

    // Channel
    .when('/shared-channel/:id/:index', {
      controller: 'SharedChannelController',
      templateUrl: 'apps/ikApp/pages/sharedChannel/shared-channel.html?' + window.config.version
    })
    .when('/channel', {
      controller: 'ChannelController',
      templateUrl: 'apps/ikApp/pages/channel/channel.html?' + window.config.version
    })
    .when('/channel/:id', {
      controller: 'ChannelController',
      templateUrl: 'apps/ikApp/pages/channel/channel.html?' + window.config.version
    })

    // Media
    .when('/media/upload', {
      controller: 'MediaUploadController',
      templateUrl: 'apps/ikApp/pages/mediaUpload/media-upload.html?' + window.config.version
    })
    .when('/media/:id', {
      controller: 'MediaEditController',
      templateUrl: 'apps/ikApp/pages/mediaEdit/media-edit.html?' + window.config.version
    })
});
