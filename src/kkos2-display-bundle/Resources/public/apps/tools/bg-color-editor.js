angular.module('toolsModule').directive('bgColorEditor', [
  'mediaFactory', function (mediaFactory) {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        close: '&',
        template: '@'
      },
      link: function (scope) {
        scope.step = 'background-picker';

        /**
         * Set the step to background-picker.
         */
        scope.backgroundPicker = function backgroundPicker() {
          scope.step = 'background-picker';
        };
      },
      templateUrl: function(elem, attrs) {
        return attrs.template ? attrs.template : '/bundles/kkos2displayintegration/apps/tools/bg-color-editor.html?v=2';
      }
    };
  }
]);

