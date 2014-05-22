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

ikApp.controller('SlideController', function($scope, $location, $routeParams, slideFactory) {
  function init() {
    if ($routeParams.step) {
      $scope.step = $routeParams.step;
    }
    else {
      $scope.step = 1;
    }

    if ($routeParams.slideId) {
      $scope.slide = slideFactory.getSlide($routeParams.slideId);

      // Make sure we are not placed at step later than what is set in the data.
      var s = 1;
      if ($scope.slide.title !== '') {
        s = s + 1;
        if ($scope.slide.orientation !== '') {
          s = s + 1;
          if ($scope.slide.template !== '') {
            s = s + 1;
          }
        }
      }

      if ($scope.step > s) {
        $location.path('slide/' + $scope.slide.id + '/' + s);
      }
    } else {
      $location.path('slide/2/1');
    }
  }
  init();

  $scope.alert = function() {
    if ($scope.step < $scope.steps) {
      $location.path('slide/' + $scope.slide.id + '/' + (parseInt($scope.step) + 1));
    } else {
      alert("DONE!");
    }
  }

  $scope.validation = {
    title: function() {
      return $scope.slide.title !== '';
    }
  };
  $scope.steps = 4;
});