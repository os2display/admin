ikApp.controller('IndexController', function($scope) {

});

ikApp.controller('ChannelsController', function($scope) {

});

ikApp.controller('SlidesController', function($scope) {

});

ikApp.controller('ScreensController', function($scope) {

});

ikApp.controller('TemplatesController', function($scope) {

});

ikApp.controller('SlideController', function($scope, $routeParams) {
  function init() {
    if ($routeParams.step) {
      $scope.step = $routeParams.step;
    }
    else {
      $scope.step = 1;
    }
  }
  init();

  $scope.steps = 4;
});