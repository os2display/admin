/**
 * Slide controller. Controls the slide creation process.
 */
ikApp.controller('SlideController', function($scope, $location, $routeParams, slideFactory, templateFactory, imageFactory) {
  /**
   * Scope setup
   */
  $scope.steps = 4;
  $scope.slide = [];
  $scope.templates = templateFactory.getTemplates();

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
      // Get the step.
      if ($routeParams.step) {
        if ($routeParams.step < 1 || $routeParams.step > $scope.steps) {
          $location.path('/slide/' + $routeParams.slideId + '/1');
          return;
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

      // Make sure we are not placed at steps later than what is set in the data.
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
        return;
      }
    }
  }
  init();

  /**
   * Submit a step in the installation process.
   */
  $scope.submitStep = function() {
    $scope.slide = slideFactory.saveSlide($scope.slide);

    // Modify history to make sure the back button does not redirect to #/slide/, so a new slide will be created.
    if ($scope.step == 1) {
      window.history.replaceState({}, "", "#/slide/" + $scope.slide.id + "/1");
    }

    if ($scope.step < $scope.steps) {
      $location.path('/slide/' + $scope.slide.id + '/' + (parseInt($scope.step) + 1));
    } else {
      $location.path('/slides');
    }
  }

  /**
   * Validates that @field is not empty on slide.
   */
  function validateNotEmpty(field) {
    if (!$scope.slide) {
      return false;
    }
    return $scope.slide[field] !== '';
  }

  /**
   * Handles the validation of the data in the slide.
   */
  $scope.validation = {
    titleSet: function() {
      return validateNotEmpty('title');
    },
    orientationSet: function() {
      return validateNotEmpty('orientation');
    },
    templateSet: function() {
      return validateNotEmpty('template');
    }
  };

  /**
   * Set the template id of a slide.
   * @param id
   */
  $scope.selectTemplate = function(id) {
    $scope.slide.template = id;
  }

  /**
   * Set the orientation of the slide.
   * @param orientation
   */
  $scope.selectOrientation = function(orientation) {
    $scope.slide.orientation = orientation;
    if (orientation === 'portrait') {
      $scope.slide.options.idealdimensions = {
        width: '1080',
        height: '1920'
      }
    } else {
      $scope.slide.options.idealdimensions = {
        width: '1920',
        height: '1080'
      }
    }
  }
});