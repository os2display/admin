  angular.module('toolsModule').directive('dingEventsEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&',
      template: '@'
    },
    templateUrl: '/bundles/kkbding2integration/apps/dingEditors/ding-events-editor.html'
  };
});
