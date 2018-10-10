angular.module('toolsModule').directive('imageFocus', [
  'mediaFactory',
  function(mediaFactory) {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&',
        template: '@'
      },
      link: function(scope) {
        scope.zoomMin = 100
        scope.zoomMax = 500
      },
      templateUrl: function(_, attrs) {
        return attrs.template
          ? attrs.template
          : '/bundles/kkbding2integration/apps/dingEditors/image-focus.html'
      }
    }
  }
])
