/**
 * @file
 * Contains the routing for the ikApp module.
 */

/**
 * Routing.
 */
angular.module('ikApp').config(function ($routeProvider, configurationProvider) {
  'use strict';

  var configuration = configurationProvider.$get();

  $routeProvider
    // Frontpage, set to channel overview.
    .when('/', {
      redirectTo: '/channel-overview'
    })

    .when('/admin-sharing', {
      controller: 'AdminSharingController',
      templateUrl: 'app/pages/adminSharing/admin-sharing.html?' + configuration.version
    })
    .when('/admin-templates', {
      controller: 'AdminTemplatesController',
      templateUrl: 'app/pages/adminTemplates/admin-templates.html?' + configuration.version
    })

    // Overviews
    .when('/channel-overview', {
      controller: 'ChannelOverviewController',
      templateUrl: 'app/pages/channelOverview/channel-overview.html?' + configuration.version
    })
    .when('/slide-overview', {
      controller: 'SlideOverviewController',
      templateUrl: 'app/pages/slideOverview/slide-overview.html?' + configuration.version
    })
    .when('/screen-overview', {
      controller: 'ScreenOverviewController',
      templateUrl: 'app/pages/screenOverview/screen-overview.html?' + configuration.version
    })
    .when('/media-overview', {
      controller: 'MediaOverviewController',
      templateUrl: 'app/pages/mediaOverview/media-overview.html?' + configuration.version
    })
    .when('/shared-channel-overview', {
      controller: 'SharedChannelOverviewController',
      templateUrl: 'app/pages/sharedChannelOverview/shared-channel-overview.html?' + configuration.version
    })

    // Screen
    .when('/screen', {
      controller: 'ScreenController',
      templateUrl: 'app/pages/screen/screen.html?' + configuration.version
    })
    .when('/screen/:id', {
      controller: 'ScreenController',
      templateUrl: 'app/pages/screen/screen.html?' + configuration.version
    })

    // Slide
    .when('/slide', {
      controller: 'SlideController',
      templateUrl: 'app/pages/slide/slide.html?' + configuration.version
    })
    .when('/slide/:id', {
      controller: 'SlideController',
      templateUrl: 'app/pages/slide/slide.html?' + configuration.version
    })

    // Channel
    .when('/shared-channel/:id/:index', {
      controller: 'SharedChannelController',
      templateUrl: 'app/pages/sharedChannel/shared-channel.html?' + configuration.version
    })
    .when('/channel', {
      controller: 'ChannelController',
      templateUrl: 'app/pages/channel/channel.html?' + configuration.version
    })
    .when('/channel/:id', {
      controller: 'ChannelController',
      templateUrl: 'app/pages/channel/channel.html?' + configuration.version
    })

    // Media
    .when('/media/upload', {
      controller: 'MediaUploadController',
      templateUrl: 'app/pages/mediaUpload/media-upload.html?' + configuration.version
    })
    .when('/media/:id', {
      controller: 'MediaEditController',
      templateUrl: 'app/pages/mediaEdit/media-edit.html?' + configuration.version
    })

    .otherwise({redirectTo: '/'});
});
