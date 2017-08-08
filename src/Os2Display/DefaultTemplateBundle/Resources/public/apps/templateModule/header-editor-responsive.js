angular.module('templateModule').directive('headerEditorResponsive', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&'
    },
    templateUrl: '/bundles/os2displaydefaulttemplate/apps/templateModule/header-editor-responsive.html'
  };
});
