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
      attrs.$observe('ikId', function(val) {
        screenFactory.getScreen(val).then(function(data) {
          scope.ikScreen = data;
        });
      });
    }
  }
}]);

