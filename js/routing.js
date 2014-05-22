ikApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider
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
      .when('/slide/:step', {
        controller: 'SlideController',
        templateUrl: 'partials/slide.html'
      })
      .otherwise({redirectTo: '/'});
  }]
);
