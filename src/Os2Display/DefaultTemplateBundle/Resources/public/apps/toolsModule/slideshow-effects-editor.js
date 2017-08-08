angular.module('toolsModule').directive('slideshowEffectsEditor', [
  function () {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&'
      },
      templateUrl: '/bundles/os2displaydefaulttemplate/apps/toolsModule/slideshow-effects-editor.html'
    };
  }
]);
