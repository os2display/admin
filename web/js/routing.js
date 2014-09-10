ikApp.config(function($routeProvider) {$routeProvider
  .when('/', {
    controller: 'IndexController',
    templateUrl: 'partials/index.html'
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

  // Screens
  .when('/screen-groups', {
    controller: 'ScreenGroupsController',
    templateUrl: 'partials/screen/screen-groups.html'
  })
  .when('/screen', {
    controller: 'ScreenController',
    templateUrl: 'partials/screen/screen.html'
  })
  .when('/screen/:id', {
    controller: 'ScreenController',
    templateUrl: 'partials/screen/screen.html'
  })
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
