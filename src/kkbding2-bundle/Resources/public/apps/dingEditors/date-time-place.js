angular.module("toolsModule").directive("dateTimePlace", function () {
  return {
    restrict: "E",
    replace: true,
    scope: {
      slide: "=",
      close: "&",
      template: "@",
    },
    templateUrl:
      "/bundles/kkbding2integration/apps/dingEditors/date-time-place.html",
  };
});
