angular.module('toolsModule').directive('slidesInSlideConfigEditor', [function(){
    return {
        restrict: 'E',
        replace: true,
        scope: {
            slide:'=',
            close: '&',
            template: '@'
        },
        templateUrl: '/bundles/os2displayslidetools/apps/tools/slides-in-slide-config-editor.html?v=1'
    };
}]);
