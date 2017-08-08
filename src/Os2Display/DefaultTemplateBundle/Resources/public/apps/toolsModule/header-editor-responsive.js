angular.module('toolsModule').directive('headerEditorResponsive', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&'
    },
    templateUrl: '/bundles/os2displaydefaulttemplate/apps/toolsModule/header-editor-responsive.html'
  };
});
