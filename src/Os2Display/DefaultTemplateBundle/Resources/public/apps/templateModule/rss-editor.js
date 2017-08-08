angular.module('templateModule').directive('rssEditor', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide:'=',
      close: '&'
    },
    templateUrl: '/bundles/os2displaydefaulttemplate/apps/templateModule/rss-editor.html'
  };
});
