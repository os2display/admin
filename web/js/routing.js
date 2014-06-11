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
  .when('/slide', {
    controller: 'SlideController',
    templateUrl: 'partials/slide1.html'
  })
  .when('/slide/:slideId/:step', {
    controller: 'SlideController',
    templateUrl: function(params){ return 'partials/slide' + ((params.step < 1) ? 1 : params.step) + '.html'; }
  })
  .otherwise({redirectTo: '/'});
});
