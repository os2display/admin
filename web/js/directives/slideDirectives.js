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
      scope.templateURL = '/partials/slide/slide-loading.html';

      // Observe for changes to the ik-id attribute. Setup slide when ik-id is set.
      attrs.$observe('ikId', function(val) {
        if (!val) {
          return;
        }

        // Get the slide.
        slideFactory.getSlide(scope.ikId).then(function(data) {
          scope.ikSlide = data;
          scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '.html';

          // Get the template.
          scope.template = templateFactory.getTemplate(scope.ikSlide.template);

          // Setup inline styling.
          scope.theStyle = {
            width: "" + scope.ikWidth + "px",
            height: "" + parseFloat(scope.template.idealdimensions.height * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px",
            fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px"
          }
        });
      });
    },
    template: '<div class="preview--slide" data-ng-include="" src="templateURL"></div>'
  }
}]);

/**
 * Directive to insert html for a slide, that is editable.
 * @param ik-id: the id of the slide.
 * @param ik-width: the width of the slide.
 */
ikApp.directive('ikSlideEditable', ['slideFactory', 'imageFactory', 'templateFactory', function(slideFactory, imageFactory, templateFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikId: '@'
    },
    link: function(scope, element, attrs) {
      scope.templateURL = '/partials/slide/slide-loading.html';

      // Observe for changes to the ik-id attribute. Setup slide when ik-id is set.
      attrs.$observe('ikId', function(val) {
        if (!val) {
          return;
        }

        slideFactory.getEditSlide(scope.ikId).then(function(data) {
          scope.ikSlide = data;
          scope.template = templateFactory.getTemplate(scope.ikSlide.template);

          scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '-edit.html';

          // Get images for editor
          imageFactory.getImages().then(function (data) {
            scope.backgroundImages = data;
          });

          // Setup editor states and functions.
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

          // Setup the inline styling
          scope.theStyle = {
            width: "" + scope.ikWidth + "px",
            height: "" + parseFloat(scope.template.idealdimensions.height * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px",
            fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px"
          }

          // Add keyup event listener for fontsize, to make sure the preview updates font size.
          element.find('.js-ik-slide-editor-fontsize-input').on('keyup', function() {
            scope.theStyle.fontsize =  "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px";
            scope.$apply();
          });

          // Cleanup.
          element.on('$destroy', function() {
            $(this).find('.js-ik-slide-editor-fontsize-input').off('keyup');
          });
        });
      });
    },
    templateUrl: '/partials/slide/slide-edit.html'
  }
}]);
