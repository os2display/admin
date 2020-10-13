angular.module("toolsModule").directive("headerEditorSimple", function () {
  return {
    restrict: "E",
    replace: true,
    scope: {
      slide: "=",
      close: "&",
      template: "@",
    },
    templateUrl:
      "/bundles/kkbding2integration/apps/dingEditors/header-editor-simple.html",
  };
});
