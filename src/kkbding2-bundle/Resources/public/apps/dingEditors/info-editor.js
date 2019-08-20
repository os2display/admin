angular.module('toolsModule').directive('infoEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&',
      template: '@'
    },
    templateUrl: '/bundles/kkbding2integration/apps/dingEditors/info-editor.html'
  };
});
