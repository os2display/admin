angular.module('toolsModule').directive('mellemfeedEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&',
      template: '@'
    },
    templateUrl: '/bundles/kkos2displayintegration/apps/tools/mellemfeed-editor.html'
  };
});
