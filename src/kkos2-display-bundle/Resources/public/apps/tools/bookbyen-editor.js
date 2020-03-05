angular.module('toolsModule').directive('bookbyenEditor', function(){
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&',
        template: '@'
      },
      templateUrl: '/bundles/kkos2displayintegration/apps/tools/bookbyen-editor.html?v=2'
    };
  }
);
