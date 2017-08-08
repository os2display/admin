angular.module('templateModule').directive('sourceEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&'
    },
    templateUrl: '/bundles/os2displaydefaulttemplate/apps/templateModule/source-editor.html'
  };
});
