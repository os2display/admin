/**
 * @file
 * Contains screen controller.
 */

/**
 * Screen controller. Controls the screen creation process.
 */
angular.module('ikApp').controller('ScreenController', ['$scope', '$location', '$routeParams', '$timeout', 'screenFactory', 'channelFactory', 'sharedChannelFactory', 'templateFactory', 'busService',
  function ($scope, $location, $routeParams, $timeout, screenFactory, channelFactory, sharedChannelFactory, templateFactory, busService) {
    'use strict';

    $scope.loading = true;
    $scope.sharingEnabled = window.config.sharingService.enabled;
    $scope.screen = {};
    $scope.toolbarTemplate = null;
    $scope.display = false;
    $scope.region = null;

    /**
     * Constructor.
     * Handles different settings of route parameters.
     */
    function init() {
      if (!$routeParams.id) {
        // If the ID is not set, get an empty slide.
        $scope.screen = screenFactory.emptyScreen();

        // Default to full-screen template if it exists, else pick the first available.
        templateFactory.getScreenTemplate('full-screen').then(
          function (data) {
            $scope.screen.template = data;
            $scope.loading = false;
          },
          function (reason) {
            if (reason === 404) {
              templateFactory.getScreenTemplates().then(
                function success(data) {
                  $scope.screen.template = angular.copy(data[0]);
                  $scope.screen.orientation = data[0].orientation;

                  $scope.loading = false;
                },
                function error(reason) {
                  busService.$emit('log.error', {
                    'cause': reason,
                    'msg': 'Skabelonerne blev ikke loaded'
                  });
                }
              );
            }
          }
        );
      }
      else {
        if ($routeParams.id === null || $routeParams.id === undefined || $routeParams.id === '') {
          $location.path('/screen-overview');
        } else {
          // Get the screen from the backend.
          screenFactory.getEditScreen($routeParams.id).then(
            function success(data) {
              $scope.loading = false;

              $scope.screen = data;

              // Decode the shared channels.
              $scope.screen.channel_screen_regions.forEach(function (csr) {
                if (csr.shared_channel) {
                  // Parse the content of the shared channel
                  //   Set title and slides of the shared_channel.
                  csr.shared_channel.content = JSON.parse(csr.shared_channel.content);
                  csr.shared_channel.title = csr.shared_channel.content.title;
                  csr.shared_channel.slides = csr.shared_channel.content.slides;
                }
              });

              if ($scope.screen === {}) {
                $location.path('/screen');
              }
            },
            // Error getting
            function error(reason) {
              busService.$emit('log.error', {
                'cause': reason,
                'msg': 'Skærmen med id: ' + $routeParams.id + ' blev ikke fundet'
              });
            }
          );
        }
      }
    }

    init();

    /**
     * Save the screen and close.
     */
    $scope.saveScreenAndClose = function saveScreenAndClose() {
      $scope.displayToolbar = false;
      $scope.region = null;
      screenFactory.saveScreen().then(
        function success(data) {
          busService.$emit('log.info', {
            'msg': 'Skærmen (' + data.title + ') er gemt',
            'timeout': 5000
          });

          // Redirect to overview.
          $timeout(function () {
            $location.path('/screen-overview');
          }, 1000);
        },
        function error(reason) {
          busService.$emit('log.error', {
            'cause': reason,
            'msg': 'Skærmen blev ikke gemt'
          });
        }
      );
    };

    /**
     * Save the screen.
     */
    $scope.saveScreen = function saveScreen() {
      $scope.displayToolbar = false;
      $scope.region = null;
      screenFactory.saveScreen().then(
        function success(data) {
          busService.$emit('log.info', {
            'msg': 'Skærmen (' + data.title + ') er gemt',
            'timeout': 5000
          });
        },
        function error(reason) {
          busService.$emit('log.error', {
            'cause': reason,
            'msg': 'Skærmen blev ikke gemt'
          });
        }
      );
    };

    /**
     * Trigger the tool defined in the tool parameter.
     * @param tool
     *   The tool and region to trigger.
     */
    $scope.triggerTool = function triggerTool(tool) {
      //Check if the current screen template provides the tool.
      if ($scope.screen.template.tools.hasOwnProperty(tool.name)) {
        $scope.toolbarTemplate = $scope.screen.template.tools[tool.name];
      }
      else {
        // Fallback to default tools.
        $scope.toolbarTemplate = 'app/shared/toolbars/' + tool.name + '.html?' + window.config.version;
      }
      $scope.region = tool.region;
      $scope.displayToolbar = true;
    };
  }
]);
