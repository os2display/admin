ikApp.controller('IndexController', function($scope) {});
ikApp.controller('ChannelsController', function($scope) {});
ikApp.controller('SlidesController', function($scope) {});
ikApp.controller('ScreensController', function($scope) {});
ikApp.controller('TemplatesController', function($scope) {});

/**
 * Slide controller. Controls the slide creation process.
 */
ikApp.controller('SlideController', function($scope, $location, $routeParams, slideFactory) {
  /**
   * Scope setup
   */
  $scope.steps = 4;

  /**
   * Constructor.
   * Handles different settings of route parameters.
   */
  function init() {
    if (!$routeParams.slideId) {
      // If the ID is not set, get an empty slide.
      $scope.slide = slideFactory.emptySlide();
      $scope.step = 1;
    } else {
      if ($routeParams.step) {
        if ($routeParams.step < 1 || $routeParams.step > $scope.steps) {
          $location.path('/slide/' + $routeParams.slideId + '/1');
        }
        else {
          $scope.step = $routeParams.step;
        }
      }
      else {
        $scope.step = 1;
      }

      // Get slide.
      $scope.slide = slideFactory.getSlide($routeParams.slideId);

      if ($scope.slide === null) {
        $location.path('/slide');
        return;
      }

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
        $location.path('/slide/' + $scope.slide.id + '/' + s);
      }

    }
  }
  init();

  /**
   * Submit a step in the installation process.
   */
  $scope.submitStep = function() {
    $scope.slide = slideFactory.saveSlide($scope.slide);

    if ($scope.step < $scope.steps) {
      $location.path('/slide/' + $scope.slide.id + '/' + (parseInt($scope.step) + 1));
    } else {
      $location.path('/slides');
    }
  }

  /**
   * Handles the validation of the data in the slide.
   * @type {{title: title}}
   */
  $scope.validation = {
    titleSet: function() {
      if (!$scope.slide) {
        return false;
      }
      return $scope.slide.title !== '';
    }
  };
});