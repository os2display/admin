/**
 * @file
 * Contains screen directives.
 */

/**
 * Directive to insert a screen.
 */
ikApp.directive('ikScreen', ['screenFactory', function(screenFactory) {
  return {
    templateUrl: 'partials/screen/screen-template.html',
    restrict: 'E',
    scope: {
      ikId: '@',
      ikWidth: '@'
    },
    link: function(scope, element, attrs) {
      screenFactory.getScreen(scope.ikId).then(function(data) {
        scope.ikScreen = data;
        scope.style = {
          width: "" + scope.ikWidth + "px",
          height: "" + (scope.ikScreen.height * parseFloat(scope.ikWidth / scope.ikScreen.width)) + "px"
        }
      });
    }
  }
}]);

