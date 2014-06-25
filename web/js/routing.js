ikApp.config(function($routeProvider) {$routeProvider
  .when('/', {
    controller: 'IndexController',
    templateUrl: 'partials/index.html'
  })
  .when('/channels', {
    controller: 'ChannelsController',
    templateUrl: 'partials/channels.html'
  })
  .when('/slides', {
    controller: 'SlidesController',
    templateUrl: 'partials/slides.html'
  })
  .when('/screens', {
    controller: 'ScreensController',
    templateUrl: 'partials/screens.html'
  })
  .when('/templates', {
    controller: 'TemplatesController',
    templateUrl: 'partials/templates.html'
  })
  .when('/screen', {
    controller: 'ScreenController',
    templateUrl: 'partials/screen1.html'
  })
  .when('/screen/:screenId/:step', {
    controller: 'ScreenController',
    templateUrl: function(params){ return 'partials/screen' + ((params.step < 1) ? 1 : params.step) + '.html'; }
  })
  .when('/slide', {
    controller: 'SlideController',
    templateUrl: 'partials/slide.html'
  })
  .when('/slide/:slideId', {
    controller: 'SlideController',
    templateUrl: 'partials/slide.html'
  })
  .when('/channel', {
    controller: 'ChannelController',
    templateUrl: 'partials/channel1.html'
  })
  .when('/channel/:channelId/:step', {
    controller: 'ChannelController',
    templateUrl: function(params){ return 'partials/channel' + ((params.step < 1) ? 1 : params.step) + '.html'; }
  })
  .when('/media', {
    controller: 'MediaController',
    templateUrl: 'partials/media.html'
  })
  .otherwise({redirectTo: '/'});
});
