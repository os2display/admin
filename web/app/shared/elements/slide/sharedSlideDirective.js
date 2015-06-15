/**
 * @file
 * Contains the ik-shared-slide directive.
 */

/**
 * Directive to insert html for a slide.
 * @param ik-id: the id of the slide.
 * @param ik-width: the width of the slide.
 */
angular.module('ikApp').directive('ikSharedSlide', ['cssInjector',
  function (cssInjector) {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikWidth: '@',
        ikSlide: '='
      },
      link: function (scope, element, attrs) {
        scope.templateURL = '/app/shared/elements/slide/slide-loading.html?' + window.config.version;

        // Observe for changes to the ik-slide attribute. Setup slide when ik-slide is set.
        attrs.$observe('ikSlide', function (val) {
          if (!val) {
            return;
          }

          cssInjector.add(scope.ikSlide.css_path);

          if (scope.ikSlide.media_thumbs && scope.ikSlide.media_thumbs.length > 0) {
            scope.ikSlide.currentImage = scope.ikSlide.media_thumbs[0];
          }

          scope.ikSlide.currentLogo = scope.ikSlide.logo;

          scope.templateURL = scope.ikSlide.preview_path;

          scope.theStyle = {
            width: "" + scope.ikWidth + "px",
            height: "" + parseFloat(1080.0 * parseFloat(scope.ikWidth / 1920.0)) + "px"
          };

          if (scope.ikSlide.options.fontsize) {
            scope.theStyle.fontsize = "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / 1920)) + "px"
          }
        });
      },
      template: '<div class="preview--slide" data-ng-include="" src="templateURL"></div>'
    }
  }
]);

