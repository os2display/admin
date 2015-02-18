/**
 * @file
 * Contains screen controller.
 */

/**
 * Screen controller. Controls the screen creation process.
 */
angular.module('ikApp').controller('ScreenController', ['$scope', '$location', '$routeParams', 'screenFactory', 'channelFactory', 'sharedChannelFactory', 'configuration', 'templateFactory',
  function($scope, $location, $routeParams, screenFactory, channelFactory, sharedChannelFactory, configuration, templateFactory) {
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

        // Get the template information, load into the $scope.screen.template
        templateFactory.getScreenTemplate($scope.screen.template).then(
          function(data) {
            $scope.screen.template = data;
          }
        );
      } else {
        if ($routeParams.id === null || $routeParams.id === undefined || $routeParams.id === '') {
          $location.path('/screen-overview');
        } else {
          // Get the screen from the backend.
          screenFactory.getEditScreen($routeParams.id).then(
            function(data) {
              $scope.screen = data;

              // To handle unset template values.
              if (!$scope.screen.template) {
                $scope.screen.template = 'full-screen';
              }

              // Get the template information, load into the $scope.screen.template
              templateFactory.getScreenTemplate($scope.screen.template).then(
                function(data) {
                  $scope.screen.template = data;
                }
              );

              $scope.screen.shared_channels.forEach(function(element) {
                element.content = JSON.parse(element.content);
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
        function(screen) {
          // @TODO: Show success to user.
          console.log("saved...");
          console.log(screen);
        },
        function(reason) {
          // @TODO: Handle error.
          console.log(reason);
        }
      );
    };

    $scope.triggerTool = function triggerTool(tool) {
      $scope.toolbarTemplate = 'app/shared/toolbars/' + tool.name + '.html';
      $scope.region = tool.region;
      $scope.displayToolbar = true;
    }
  }
]);
