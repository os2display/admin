angular.module('toolsModule').directive('sourceEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&'
    },
    templateUrl: '/bundles/os2displaydefaulttemplate/apps/toolsModule/source-editor.html'
  };
});
