angular.module('toolsModule').directive('twothreevideoEditor', function(){
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&',
        template: '@'
      },
      templateUrl: '/bundles/kkos2displayintegration/apps/tools/twothreevideo-editor.html?v=1'
    };
  }
);
