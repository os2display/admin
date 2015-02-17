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
    $scope.screenEditorTemplate = '';
    $scope.toolbarTemplate = null;
    $scope.editRegion = null;

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
          $location.path('/screen');
        } else {
          // Get the screen from the backend.
          screenFactory.getEditScreen($routeParams.id).then(
            function(data) {
              $scope.screen = data;

              $scope.screen.shared_channels.forEach(function(element) {
                element.content = JSON.parse(element.content);
              });

              if ($scope.screen === {}) {
                $location.path('/screen');
              }
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
      console.log("saving...");

      screenFactory.saveScreen().then(
        function() {
          // @TODO: Show success to user.
        },
        function() {
          // @TODO: Handle error.
        }
      );
    };

    $scope.triggerTool = function triggerTool(tool) {
      $scope.toolbarTemplate = 'app/shared/toolbars/' + tool.name + '.html';
      $scope.editRegion = tool.region;
    }
  }
]);
