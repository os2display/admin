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

          if (scope.ikSlide.options.images.length > 0) {
            scope.ikSlide.currentImage = scope.ikSlide.imageUrls[scope.ikSlide.options.images[0]]['default_landscape_small'];
          } else {
            scope.ikSlide.currentImage = '';
          }

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
ikApp.directive('ikSlideEditable', ['slideFactory', 'mediaFactory', 'templateFactory', function(slideFactory, mediaFactory, templateFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikId: '@'
    },
    link: function(scope, element, attrs) {
      scope.templateURL = '/partials/slide/slide-loading.html';

      // Watch for changes to ikSlide.
      scope.$watch('ikSlide', function (newVal, oldVal) {
        if (!newVal) return;

        // If the background color has changed, remove selected images.
        if (newVal.options.bgcolor !== oldVal.options.bgcolor) {
          scope.ikSlide.options.images = [];
        }

        // Update image to show.
        if (scope.ikSlide.options.images.length > 0) {
          scope.ikSlide.currentImage = scope.ikSlide.imageUrls[scope.ikSlide.options.images[0]]['default_landscape'];
        } else {
          scope.ikSlide.currentImage = '';
        }
      }, true);

      // Observe for changes to the ik-id attribute. Setup slide when ik-id is set.
      attrs.$observe('ikId', function(val) {
        slideFactory.getEditSlide(scope.ikId).then(function(data) {
          scope.ikSlide = data;
          scope.template = templateFactory.getTemplate(scope.ikSlide.template);

          scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '-edit.html';

          // Find the current image to display.
          if (scope.ikSlide.options.images.length > 0) {
            scope.ikSlide.currentImage = scope.ikSlide.imageUrls[scope.ikSlide.options.images[0]]['default_landscape'];
          } else {
            scope.ikSlide.currentImage = '';
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
