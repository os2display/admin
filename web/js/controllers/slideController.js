/**
 * Slide controller. Controls the slide creation process.
 */
ikApp.controller('SlideController', function($scope, $location, $routeParams, slideFactory, templateFactory) {
  /**
   * Scope setup
   */
  $scope.steps = 4;
  $scope.slide = {};
  $scope.templates = templateFactory.getTemplates();

  function loadStep(step) {
    $scope.step = step;
    $scope.templatePath = '/partials/slide' + $scope.step + '.html';
  }

  /**
   * Constructor.
   * Handles different settings of route parameters.
   */
  function init() {
    if (!$routeParams.slideId) {
      // If the ID is not set, get an empty slide.
      $scope.slide = slideFactory.emptySlide();
      loadStep(1);
    } else {
      if ($routeParams.slideId == null || $routeParams.slideId == undefined || $routeParams.slideId == '') {
        $location.path('/slide');
      } else {
        // Get the slide from the backend.
        slideFactory.getEditSlide($routeParams.slideId).then(function(data) {
          $scope.slide = data;

          if ($scope.slide === {}) {
            $location.path('/slide');
          }

          loadStep($scope.steps);
        });
      }
    }
  }
  init();

  /**
   * Submit a step in the installation process.
   */
  $scope.submitStep = function() {
    if ($scope.step == $scope.steps) {
      slideFactory.saveSlide($scope.slide).then(function() {
        $location.path('/slides');
      });
    } else {
      loadStep($scope.step + 1);
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