/**
 * Directive to insert a screen.
 */
ikApp.directive('ikScreen', ['screenFactory', function(screenFactory) {
  return {
    templateUrl: 'partials/screen.html',
    restrict: 'E',
    scope: {},
    link: function(scope, element, attrs) {
      scope.ikScreen = screenFactory.getScreen(attrs.ikId);
      scope.ikScreenGroups = screenFactory.getScreenGroups(attrs.ikId);
      scope.ikGroups = screenFactory.getGroups();
    }
  }
}]);

