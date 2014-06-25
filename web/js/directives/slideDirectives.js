/**
 * Directive to insert html for a slide.
 * @param ik-id: the id of the slide.
 * @param ik-width: the width of the slide.
 */
ikApp.directive('ikSlide', ['slideFactory', 'templateFactory', function(slideFactory, templateFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikId: '@'
    },
    link: function(scope, element, attrs) {
      slideFactory.getSlide(scope.ikId).then(function(data) {
        scope.ikSlide = data;
        scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '.html';

        scope.theStyle = {
          width: "" + scope.ikWidth + "px",
          height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
          fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
        }
      });
    },
    template: '<div data-ng-include="" src="templateURL" include-replace></div>'
  }
}]);

/**
 * Directive to insert html for a slide, that is editable.
 * @param ik-id: the id of the slide.
 * @param ik-width: the width of the slide.
 */
ikApp.directive('ikSlideEditable', ['slideFactory', 'imageFactory', function(slideFactory, imageFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikId: '@'
    },
    link: function(scope, element, attrs) {
      attrs.$observe('ikId', function(val) {
        slideFactory.getEditSlide(scope.ikId).then(function(data) {
          scope.ikSlide = data;

          scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '-edit.html';
          scope.backgroundImages = imageFactory.getImages();
          scope.editor = {
            showTextEditor: false,
            toggleTextEditor: function() {
              scope.editor.showBackgroundEditor = false;
              scope.editor.showTextEditor = !scope.editor.showTextEditor;
            },
            showBackgroundEditor: false,
            toggleBackgroundEditor: function() {
              scope.editor.showTextEditor = false;
              scope.editor.showBackgroundEditor = !scope.editor.showBackgroundEditor;
            }
          }

          scope.theStyle = {
            width: "" + scope.ikWidth + "px",
            height: "" + parseFloat(scope.ikSlide.options.idealdimensions.height * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px",
            fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px"
          }

          // Add keyup event listener for fontsize, to make sure the preview updates font size.
          element.find('.js-ik-slide-editor-fontsize-input').on('keyup', function() {
            scope.theStyle.fontsize =  "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.ikSlide.options.idealdimensions.width)) + "px";
            scope.$apply();
          });

          // Cleanup.
          element.on('$destroy', function() {
            $(this).find('.js-ik-slide-editor-fontsize-input').off('keyup');
          });
        });
      });
    },
    templateUrl: '/partials/slide-edit.html'
  }
}]);
