angular.module('toolsModule').directive('eventFeedEditor', function(){
    return {
        restrict: 'E',
        replace: true,
        scope: {
            slide:'=',
            close: '&',
            template: '@'
        },
        templateUrl: '/bundles/kkos2displayintegration/apps/tools/event-feed-editor.html'
    };
});
