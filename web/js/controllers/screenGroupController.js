/**
 * Screen group controller. Controls the screen group creation process.
 */
ikApp.controller('ScreenGroupController', function($scope, $location, $routeParams, screenFactory) {
  /**
   * Scope setup
   */
  $scope.steps = 2;
  $scope.screenGroup = {};

  screenFactory.getScreens().then(function(data) {
    $scope.screens = data;
  });

  /**
   * Loads a given step
   */
  function loadStep(step) {
    $scope.step = step;
    $scope.templatePath = '/partials/screen/screen-group' + $scope.step + '.html';
  }

  /**
   * Constructor.
   * Handles different settings of route parameters.
   */
  function init() {
    if (!$routeParams.id) {
      // If the ID is not set, get an empty slide.
      $scope.screenGroup = screenFactory.emptyScreenGroup();
      loadStep(1);
    } else {
      if ($routeParams.id == null || $routeParams.id == undefined || $routeParams.id == '') {
        $location.path('/screen-group');
      } else {
        // Get the screen from the backend.
        screenFactory.getEditScreenGroup($routeParams.id).then(function(data) {
          $scope.screenGroup = data;

          if ($scope.screen === {}) {
            $location.path('/screen-group');
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
      screenFactory.saveScreenGroup().then(
        function() {
          $location.path('/screen-groups');
        }
      );
    } else {
      loadStep($scope.step + 1);
    }
  }

  /**
   * Set the orientation of the screen.
   * @param orientation
   */
  $scope.setOrientation = function(orientation) {
    $scope.screen.orientation = orientation;
  }

  /**
   * Move to @step in creation process.
   * @param step
   */
  $scope.goToStep = function(step) {
    var s = 1;
    if ($scope.validation.titleSet()) {
      s++;
    }
    if (step <= s) {
      loadStep(step);
    }
  };

  /**
   * Validates that @field is not empty on screen.
   */
  function validateNotEmpty(field) {
    if (!$scope.screenGroup) {
      return false;
    }
    return $scope.screenGroup[field] !== '';
  }

  /**
   * Handles the validation of the data in the screen.
   */
  $scope.validation = {
    titleSet: function() {
      return validateNotEmpty('title');
    }
  };

});