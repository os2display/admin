/**
 * @file
 * Contains screen controller.
 */

/**
 * Screen controller. Controls the screen creation process.
 */
angular.module('ikApp').controller('ScreenController', ['$scope', '$location', '$routeParams', 'screenFactory', 'channelFactory', 'sharedChannelFactory', 'configuration',
  function($scope, $location, $routeParams, screenFactory, channelFactory, sharedChannelFactory, configuration) {
    $scope.sharingEnabled = configuration.sharingService.enabled;
    $scope.screen = {};
    $scope.toolbarTemplate = null;
    $scope.displayToolbar = false;
    $scope.region = null;

    /**
     * Constructor.
     * Handles different settings of route parameters.
     */
    function init() {
      if (!$routeParams.id) {
        // If the ID is not set, get an empty slide.
        $scope.screen = screenFactory.emptyScreen();
      } else {
        if ($routeParams.id === null || $routeParams.id === undefined || $routeParams.id === '') {
          $location.path('/screen-overview');
        } else {
          // Get the screen from the backend.
          screenFactory.getEditScreen($routeParams.id).then(
            function(data) {
              $scope.screen = data;

              // Decode the shared channels.
              $scope.screen.channel_screen_regions.forEach(function(csr) {
                if (csr.shared_channel) {
                  csr.shared_channel.content = JSON.parse(csr.shared_channel.content);
                }
              });

              if ($scope.screen === {}) {
                $location.path('/screen');
              }
            },
            // Error getting
            function(reason) {
              console.log(reason);
            }
          );
        }
      }
    }
    init();

    /**
     * Save the screen.
     */
    $scope.saveScreen = function saveScreen() {
      screenFactory.saveScreen().then(
        function() {
          $scope.displayToolbar = false;
          $scope.region = null;
        },
        function(reason) {
          // @TODO: Handle error.
          console.log(reason);
        }
      );
    };

    /**
     * Trigger the tool defined in the tool parameter.
     * @param tool
     *   The tool and region to trigger.
     */
    $scope.triggerTool = function triggerTool(tool) {
      $scope.toolbarTemplate = 'app/shared/toolbars/' + tool.name + '.html';
      $scope.region = tool.region;
      $scope.displayToolbar = true;
    }
  }
]);
