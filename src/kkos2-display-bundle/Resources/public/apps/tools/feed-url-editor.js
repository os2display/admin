angular.module('toolsModule').directive('feedUrlEditor', function(){
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&',
        template: '@'
      },
      templateUrl: '/bundles/kkos2displayintegration/apps/tools/feed-url-editor.html'
    };
  }
);
