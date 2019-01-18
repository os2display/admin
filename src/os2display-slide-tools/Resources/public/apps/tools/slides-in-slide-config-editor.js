angular.module('toolsModule').directive('slidesInSlides', function(){
    return {
        restrict: 'E',
        replace: true,
        scope: {
            slide:'=',
            close: '&',
            template: '@'
        },
        templateUrl: '/bundles/os2displayslidetools/apps/tools/slides-in-slide-config-editor.html'
    };
}) .controller('slidesInSlidesController', ['$scope', function($scope) {
    $scope.eventBox = function() {
        var options = $scope.ikSlide.options;
        $scope.sisBoxHeight = false;
        $scope.sisBoxScale = '1';
        $scope.sisSubslidesPrSlide = options.sis_items_pr_slide;
    }
}]);