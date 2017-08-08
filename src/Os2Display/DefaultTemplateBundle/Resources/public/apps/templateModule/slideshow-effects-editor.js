angular.module('templateModule').directive('slideshowEffectsEditor', [
  function () {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&'
      },
      templateUrl: '/bundles/os2displaydefaulttemplate/apps/templateModule/slideshow-effects-editor.html'
    };
  }
]);
