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
      templateUrl: 'app/pages/adminSharing/admin-sharing.html'
    })

    // Overviews
    .when('/channel-overview', {
      controller: 'ChannelOverviewController',
      templateUrl: 'app/pages/channelOverview/channel-overview.html'
    })
    .when('/slide-overview', {
      controller: 'SlideOverviewController',
      templateUrl: 'app/pages/slideOverview/slide-overview.html'
    })
    .when('/screen-overview', {
      controller: 'ScreenOverviewController',
      templateUrl: 'app/pages/screenOverview/screen-overview.html'
    })
    .when('/media-overview', {
      controller: 'MediaOverviewController',
      templateUrl: 'app/pages/mediaOverview/media-overview.html'
    })
    .when('/shared-channel-overview', {
      controller: 'SharedChannelOverviewController',
      templateUrl: 'app/pages/sharedChannelOverview/shared-channel-overview.html'
    })

    // Screen
    .when('/screen', {
      controller: 'ScreenController',
      templateUrl: 'app/pages/screen/screen.html'
    })
    .when('/screen/:id', {
      controller: 'ScreenController',
      templateUrl: 'app/pages/screen/screen.html'
    })

    // Slide
    .when('/slide', {
      controller: 'SlideController',
      templateUrl: 'app/pages/slide/slide.html'
    })
    .when('/slide/:id', {
      controller: 'SlideController',
      templateUrl: 'app/pages/slide/slide.html'
    })

    // Channel
    .when('/shared-channel/:id/:index', {
      controller: 'SharedChannelController',
      templateUrl: 'app/pages/sharedChannel/shared-channel.html'
    })
    .when('/channel', {
      controller: 'ChannelController',
      templateUrl: 'app/pages/channel/channel.html'
    })
    .when('/channel/:id', {
      controller: 'ChannelController',
      templateUrl: 'app/pages/channel/channel.html'
    })

    // Media
    .when('/media/upload', {
      controller: 'MediaUploadController',
      templateUrl: 'app/pages/mediaUpload/media-upload.html'
    })
    .when('/media/:id', {
      controller: 'MediaEditController',
      templateUrl: 'app/pages/mediaEdit/media-edit.html'
    })

    .otherwise({redirectTo: '/'});
});
