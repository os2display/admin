angular.module('defaultToolsModule').directive('textEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&'
    },
    templateUrl: '/bundles/os2displaydefaulttemplate/apps/defaultToolsModule/text-editor.html'
  };
});
