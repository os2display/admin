angular.module('toolsModule').directive('referenceEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&',
      template: '@'
    },
    templateUrl: '/bundles/kkbding2integration/apps/dingEditors/reference-editor.html'
  };
});
