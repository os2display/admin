/**
 * @file
 * Channel creation controllers.
 */

/**
 * Shared channel controller. Controls the channel creation process.
 */
ikApp.controller('SharedChannelController', ['$scope', '$location', '$routeParams', '$timeout', 'screenFactory', 'sharedChannelFactory',
  function($scope, $location, $routeParams, $timeout, screenFactory, sharedChannelFactory) {
    $scope.steps = 2;
    $scope.step = 1;
    $scope.screens = [];
    $scope.channel = {};
    $scope.channel.slides = [];
    $scope.status = 'edit';

    // Get all screens.
    screenFactory.getScreens().then(function (data) {
      $scope.screens = data;
    });

    /**
     * Loads a given step.
     */
    function loadStep(step) {
      $scope.step = step;
      $scope.templatePath = '/partials/channel-sharing/shared-channel-step' + $scope.step + '.html';
    }

    /**
     * Constructor.
     * Handles different settings of route parameters.
     */
    function init() {
      if (!$routeParams.id || !$routeParams.ownerId) {
        $location.path('/channel-sharing-overview');
      } else {
        sharedChannelFactory.getSharedChannel($routeParams.id, $routeParams.ownerId).then(function(data) {
          $scope.channel = data;

          if ($scope.channel === {}) {
            $location.path('/channel-sharing-overview');
          }

          loadStep(1);
        });
      }
    }
    init();

    /**
     * Submit a step in the installation process.
     */
    $scope.submitStep = function() {
      if ($scope.step == $scope.steps) {
      } else {
        loadStep($scope.step + 1);
      }
    };

    /**
     * Is the screen selected?
     * @param id
     * @returns {boolean}
     */
    $scope.screenSelected = function(id) {
      var res = false;

      $scope.channel.screens.forEach(function(element) {
        if (id == element.id) {
          res = true;
        }
      });

      return res;
    };

    /**
     * Select or deselect the screens related to a channel.
     * @param screen
     */
    $scope.toggleScreen = function(screen) {
      var res = false;

      $scope.channel.screens.forEach(function(element, index, array) {
        if (screen.id == element.id) {
          res = true;
        }
      });

      if (res) {
        $scope.channel.screens.splice($scope.channel.screens.indexOf(screen), 1);
      }
      else {
        $scope.channel.screens.push(screen);
      }
    };

    /**
     * Change channel creation step.
     * @param step
     */
    $scope.goToStep = function(step) {
      var s = 1;
      // If title is set enable next step.
      if ($scope.validation.titleSet()) {
        s++;
        // If orientation is set enable next three steps.
        if ($scope.validation.orientationSet()) {
          s = s + 3;
        }
      }
      if (step <= s) {
        loadStep(step);
      }
    };
  }
]);