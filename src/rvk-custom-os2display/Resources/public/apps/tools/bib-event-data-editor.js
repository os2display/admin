angular.module("toolsModule").directive("bibEventDataEditor", [function(){
    return {
        restrict: "E",
        replace: true,
        scope: {
            slide:"=",
            close: "&",
            template: "@"
        },
        templateUrl: "/bundles/rvkcustomos2display/apps/tools/bib-event-data-editor.html?v=1"
    };
}]);
