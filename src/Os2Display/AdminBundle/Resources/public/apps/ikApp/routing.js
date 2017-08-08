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
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/adminSharing/admin-sharing.html?' + window.config.version
    })
    .when('/admin-templates', {
      controller: 'AdminTemplatesController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/adminTemplates/admin-templates.html?' + window.config.version
    })

    // Overviews
    .when('/channel-overview', {
      controller: 'ChannelOverviewController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/channelOverview/channel-overview.html?' + window.config.version
    })
    .when('/slide-overview', {
      controller: 'SlideOverviewController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/slideOverview/slide-overview.html?' + window.config.version
    })
    .when('/screen-overview', {
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/screenOverview/screen-overview.html?' + window.config.version
    })
    .when('/media-overview', {
      controller: 'MediaOverviewController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/mediaOverview/media-overview.html?' + window.config.version
    })
    .when('/shared-channel-overview', {
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/sharedChannelOverview/shared-channel-overview.html?' + window.config.version
    })

    // Screen
    .when('/screen', {
      controller: 'ScreenController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/screen/screen.html?' + window.config.version
    })
    .when('/screen/:id', {
      controller: 'ScreenController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/screen/screen.html?' + window.config.version
    })

    // Slide
    .when('/slide', {
      controller: 'SlideController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/slide/slide.html?' + window.config.version
    })
    .when('/slide/:id', {
      controller: 'SlideController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/slide/slide.html?' + window.config.version
    })

    // Channel
    .when('/shared-channel/:id/:index', {
      controller: 'SharedChannelController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/sharedChannel/shared-channel.html?' + window.config.version
    })
    .when('/channel', {
      controller: 'ChannelController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/channel/channel.html?' + window.config.version
    })
    .when('/channel/:id', {
      controller: 'ChannelController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/channel/channel.html?' + window.config.version
    })

    // Media
    .when('/media/upload', {
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/mediaUpload/media-upload.html?' + window.config.version
    })
    .when('/media/:id', {
      controller: 'MediaEditController',
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/pages/mediaEdit/media-edit.html?' + window.config.version
    })
});
