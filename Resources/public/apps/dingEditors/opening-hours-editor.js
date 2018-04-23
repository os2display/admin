angular.module('toolsModule').directive('openingHoursEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&',
      template: '@'
    },
    templateUrl: '/bundles/kkbding2integration/apps/dingEditors/opening-hours-editor.html'
  };
});
