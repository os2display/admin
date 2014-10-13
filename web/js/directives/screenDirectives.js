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
      ikScreen: '=',
      ikWidth: '@'
    },
    link: function(scope, element, attrs) {
      // Observe for changes to the ikScreen attribute.
      attrs.$observe('ikScreen', function(val) {
        if (!val) {
          return;
        }

        // Set the style of the screen.
        scope.style = {
          width: "" + scope.ikWidth + "px",
          height: "" + (scope.ikScreen.height * parseFloat(scope.ikWidth / scope.ikScreen.width)) + "px"
        }
      });
    }
  }
}]);

