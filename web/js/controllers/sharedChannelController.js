/**
 * @file
 * Shared channel creation controllers.
 */

/**
 * Shared channel controller. Controls the channel creation process.
 */
angular.module('ikApp').controller('SharedChannelController', ['$scope', '$location', '$routeParams', '$timeout', 'screenFactory', 'sharedChannelFactory',
  function($scope, $location, $routeParams, $timeout, screenFactory, sharedChannelFactory) {
    $scope.steps = 2;
    $scope.step = 1;
    $scope.screens = [];
    $scope.channel = {};
    $scope.sharedChannel = {};
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
      if (!$routeParams.id || !$routeParams.index) {
        $location.path('/channel-sharing-overview');
      } else {
        sharedChannelFactory.getSharedChannel($routeParams.id, $routeParams.index).then(function(data) {
          $scope.channel = JSON.parse(data.content);
          $scope.sharedChannel = data;

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
      // If last step, save shared channel.
      if ($scope.step == $scope.steps) {
        sharedChannelFactory.saveSharedChannel($scope.sharedChannel).then(function() {
          $location.path('/channel-sharing-overview');
        });
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

      $scope.sharedChannel.screens.forEach(function(element) {
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

      $scope.sharedChannel.screens.forEach(function(element, index, array) {
        if (screen.id == element.id) {
          res = true;
        }
      });

      if (res) {
        $scope.sharedChannel.screens.splice($scope.sharedChannel.screens.indexOf(screen), 1);
      }
      else {
        $scope.sharedChannel.screens.push(screen);
      }
    };

    /**
     * Change channel creation step.
     * @param step
     */
    $scope.goToStep = function(step) {
      loadStep(step);
    };
  }
]);