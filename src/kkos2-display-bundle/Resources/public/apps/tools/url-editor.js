angular.module('toolsModule').directive('urlEditor', function(){
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&',
        template: '@'
      },
      templateUrl: '/bundles/kkos2displayintegration/apps/tools/url-editor.html'
    };
  }
);
