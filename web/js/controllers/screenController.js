/**
 * @file
 * Contains screen controllers.
 */

/**
 * Screen controller. Controls the screen creation process.
 */
ikApp.controller('ScreenController', ['$scope', '$location', '$routeParams', '$timeout', 'screenFactory', 'channelFactory', 'sharedChannelFactory',
  function($scope, $location, $routeParams, $timeout, screenFactory, channelFactory, sharedChannelFactory) {
    $scope.steps = 3;
    $scope.screen = {};

    // Setup the editor.
    $scope.editor = {
      channelOverviewEditor: false,
      sharedChannelOverviewEditor: false,
      showSharedChannels: false,
      toggleChannelOverviewEditor: function() {
        $('html').toggleClass('is-locked');
        $scope.editor.channelOverviewEditor = !$scope.editor.channelOverviewEditor;
      },
      toggleSharedChannelOverviewEditor: function() {
        $('html').toggleClass('is-locked');
        $scope.editor.sharedChannelOverviewEditor = !$scope.editor.sharedChannelOverviewEditor;
      }
    };

    // Register event listener for clickSlide.
    $scope.$on('channelOverview.clickChannel', function(event, channel) {
      $scope.toggleChannel(channel);
    });

    // Register event listener for clickSlide.
    $scope.$on('channelSharingOverview.clickSharedChannel', function(event, channel, index) {
      $scope.toggleSharedChannel(channel, index);
    });

    /**
     * Loads a given step
     */
    function loadStep(step) {
      $scope.step = step;
      $scope.templatePath = '/partials/screen/screen' + $scope.step + '.html';
    }

    /**
     * Constructor.
     * Handles different settings of route parameters.
     */
    function init() {
      if (!$routeParams.id) {
        // If the ID is not set, get an empty slide.
        $scope.screen = screenFactory.emptyScreen();
        loadStep(1);
      } else {
        if ($routeParams.id == null || $routeParams.id == undefined || $routeParams.id == '') {
          $location.path('/screen');
        } else {
          // Get the screen from the backend.
          screenFactory.getEditScreen($routeParams.id).then(function(data) {
            $scope.screen = data;

            $scope.screen.shared_channels.forEach(function(element) {
              element.content = JSON.parse(element.content);
            });

            $scope.screen.status = 'edit-screen';

            if ($scope.screen === {}) {
              $location.path('/screen');
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
        $scope.disableSubmitButton = true;

        screenFactory.saveScreen().then(
          function() {
            $timeout(function() {
              $location.path('/screen-overview');
            }, 1000);
          },
          function() {
            $scope.disableSubmitButton = false;
          });
      } else {
        loadStep($scope.step + 1);
      }
    };

    /**
     * Set the orientation of the screen.
     * @param orientation
     */
    $scope.setOrientation = function(orientation) {
      $scope.screen.orientation = orientation;
    };

    /**
     * Load a step. Checks that the previous steps have been filled out.
     * @param step
     */
    $scope.goToStep = function(step) {
      var s = 1;
      if ($scope.validation.titleSet()) {
        s++;
        if ($scope.validation.widthSet() && $scope.validation.heightSet()) {
          s++;
        }
      }
      if (step <= s) {
        loadStep(step);
      }
    };

    /**
     * Validates that @field is not empty on screen.
     */
    function validateNotEmpty(field) {
      if (!$scope.screen) {
        return false;
      }
      return $scope.screen[field] !== '';
    }

    /**
     * Handles the validation of the data in the screen.
     */
    $scope.validation = {
      titleSet: function() {
        return validateNotEmpty('title');
      },
      widthSet: function() {
        return (/^\d+$/.test($scope.screen.width));
      },
      heightSet: function() {
        return (/^\d+$/.test($scope.screen.height));
      }
    };

    /**
     * Check if channel is included in the current screen.
     * @param channel
     * @returns {boolean}
     */
    $scope.hasChannel = function hasChannel(channel) {
      var res = false;

      $scope.screen.channels.forEach(function(element) {
        if (channel.id == element.id) {
          res = true;
        }
      });
      return res;
    };


    /**
     * Add remove a channel.
     * @param channel
     */
    $scope.toggleChannel = function(channel) {
      var index = null;

      $scope.screen.channels.forEach(function(slideChannel, channelIndex) {
        if (channel.id == slideChannel.id) {
          index = channelIndex;
        }
      });

      if (index !== null) {
        $scope.screen.channels.splice(index, 1);
      }
      else {
        $scope.screen.channels.push(channel);
      }
    };

    /**
     * Add remove a channel.
     * @param channel
     */
    $scope.toggleSharedChannel = function(channel, channel_index) {
      var index = null;

      sharedChannelFactory.getSharedChannel(channel.unique_id, channel_index).then(function(data) {
        channel = data;
        channel.content = JSON.parse(channel.content);

        $scope.screen.shared_channels.forEach(function(slideChannel, channelIndex) {
          if (channel.unique_id === slideChannel.unique_id) {
            index = channelIndex;
          }
        });

        if (index !== null) {
          $scope.screen.shared_channels.splice(index, 1);
        }
        else {
          $scope.screen.shared_channels.push(channel);
        }
      });
    };
  }
]);
