angular.module('toolsModule').directive('eventPlaceEditor', function() {
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide: '=',
      close: '&',
      template: '@'
    },
    templateUrl:
      '/bundles/kkbding2integration/apps/dingEditors/event-place-editor.html'
  }
})
