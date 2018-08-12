angular.module('toolsModule').directive('headerStaticColors', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&',
      template: '@'
    },
    templateUrl: '/bundles/kkbding2integration/apps/dingEditors/header-static-colors.html'
  };
});
