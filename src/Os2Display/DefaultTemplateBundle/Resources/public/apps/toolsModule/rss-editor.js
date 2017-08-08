angular.module('toolsModule').directive('rssEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&'
    },
    templateUrl: '/bundles/os2displaydefaulttemplate/apps/toolsModule/rss-editor.html'
  };
});
