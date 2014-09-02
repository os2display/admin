/**
 * Slide controller. Controls the slide creation process.
 */
ikApp.controller('SlideController', function($scope, $location, $routeParams, $timeout, slideFactory, templateFactory) {
  /**
   * Scope setup
   */
  $scope.steps = 4;
  $scope.slide = {};
  $scope.templates = templateFactory.getTemplates();


  /**
   * Loads a given step
   */
  function loadStep(step) {
    $scope.step = step;
    $scope.templatePath = '/partials/slide/slide' + $scope.step + '.html';
  }

  /**
   * Constructor.
   * Handles different settings of route parameters.
   */
  function init() {
    if (!$routeParams.id) {
      // If the ID is not set, get an empty slide.
      $scope.slide = slideFactory.emptySlide();
      loadStep(1);
    } else {
      if ($routeParams.id == null || $routeParams.id == undefined || $routeParams.id == '') {
        $location.path('/slide');
      } else {
        // Get the slide from the backend.
        slideFactory.getEditSlide($routeParams.id).then(function(data) {
          $scope.slide = data;
          $scope.slide.status = 'edit-slide';
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
      slideFactory.saveSlide().then(function() {
        $timeout(function() {
          $location.path('/slide-overview');
        }, 500);
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
   * Go the given step in the creation process, if the requirements have been met to be at that step.
   * @param step
   */
  $scope.goToStep = function(step) {
    var s = 1;
    if ($scope.validation.titleSet()) {
      s++;
      if ($scope.validation.orientationSet()) {
        s++;
        if ($scope.validation.templateSet()) {
          s++;
        }
      }
    }
    if (step <= s) {
      loadStep(step);
    }
  };

  /**
   * Set the template id of a slide.
   * Update the options attribute to add fields that are needed for the template.
   *
   * @param id
   */
  $scope.selectTemplate = function(id) {
    $scope.slide.template = id;
    if ($scope.slide.options == null) {
      $scope.slide.options = templateFactory.getTemplate(id).emptyoptions;
    }
    else {
      angular.forEach(templateFactory.getTemplate(id).emptyoptions, function(value, key)  {
        if ($scope.slide.options[key] == undefined) {
          $scope.slide.options[key] = value;
        }
      });
    }
  }

  /**
   * Set the orientation of the slide.
   * @param orientation
   */
  $scope.selectOrientation = function(orientation) {
    $scope.slide.orientation = orientation;
    $scope.slide.template = '';
  }
});