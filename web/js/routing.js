/**
 * @file
 * Contains the routing of the angular app.
 */

/**
 * Routing.
 */
angular.module('ikApp').config(function($routeProvider) {
  $routeProvider
    // Frontpage, set to channel overview.
    .when('/', {
      redirectTo: '/channel-overview'
    })

    .when('/admin-sharing', {
      controller: 'AdminSharingController',
      templateUrl: 'partials/channel-sharing/admin-sharing.html'
    })

    // Overviews
    .when('/channel-overview', {
      controller: 'ChannelOverviewController',
      templateUrl: 'partials/channel/channel-overview.html'
    })
    .when('/slide-overview', {
      controller: 'SlideOverviewController',
      templateUrl: 'partials/slide/slide-overview.html'
    })
    .when('/screen-overview', {
      controller: 'ScreenOverviewController',
      templateUrl: 'partials/screen/screen-overview.html'
    })
    .when('/media-overview', {
      controller: 'MediaOverviewController',
      templateUrl: 'partials/media/media-overview.html'
    })
    .when('/channel-sharing-overview', {
      controller: 'ChannelSharingOverviewController',
      templateUrl: 'partials/channel-sharing/channel-sharing-overview.html'
    })

    // Screen
    .when('/screen', {
      controller: 'ScreenController',
      templateUrl: 'partials/screen/screen.html'
    })
    .when('/screen/:id', {
      controller: 'ScreenController',
      templateUrl: 'partials/screen/screen.html'
    })

    // Screen group
    .when('/screen-group', {
      controller: 'ScreenGroupController',
      templateUrl: 'partials/screen/screen-group.html'
    })
    .when('/screen-group/:id', {
      controller: 'ScreenGroupController',
      templateUrl: 'partials/screen/screen-group.html'
    })

    // Slide
    .when('/slide', {
      controller: 'SlideController',
      templateUrl: 'partials/slide/slide.html'
    })
    .when('/slide/:id', {
      controller: 'SlideController',
      templateUrl: 'partials/slide/slide.html'
    })

    // Channel
    .when('/shared-channel/:id/:index', {
      controller: 'SharedChannelController',
      templateUrl: 'partials/channel-sharing/shared-channel.html'
    })
    .when('/channel', {
      controller: 'ChannelController',
      templateUrl: 'partials/channel/channel.html'
    })
    .when('/channel/:id', {
      controller: 'ChannelController',
      templateUrl: 'partials/channel/channel.html'
    })

    // Media
    .when('/media/upload', {
      controller: 'MediaUploadController',
      templateUrl: 'partials/media/media-upload.html'
    })
    .when('/media/:id', {
      controller: 'MediaEditController',
      templateUrl: 'partials/media/media-edit.html'
    })

    .otherwise({redirectTo: '/'});
});
