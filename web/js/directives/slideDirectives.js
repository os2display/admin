/**
 * @file
 * Contains slide directives to display and edit a slide.
 */

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
      ikSlide: '='
    },
    link: function(scope, element, attrs) {
      scope.templateURL = '/partials/slide/slide-loading.html';

      // Observe for changes to the ik-slide attribute. Setup slide when ik-slide is set.
      attrs.$observe('ikSlide', function(val) {
        if (!val) {
          return;
        }

        scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '.html';

        if (scope.ikSlide.media_type === 'image') {
          if (scope.ikSlide.media_orders.length > 0) {
            scope.ikSlide.currentImage = scope.ikSlide.media_orders[0].media.urls.default_landscape_small;
          }
          else {
            scope.ikSlide.currentImage = '';
          }
        }
        else {
          if (scope.ikSlide.media_orders.length > 0) {
            if (scope.ikSlide.media[0] === undefined) {
              scope.ikSlide.currentVideo = {"mp4": "", "ogg": ""};
            }
            else {
              // @TODO: test this!!
              scope.ikSlide.currentVideo = scope.ikSlide.media_orders[0].media;

              // Reload video player.
              setTimeout(function () {
                element.find('#videoPlayer').load();
              }, 1000);
            }
          }
          else {
            scope.ikSlide.currentVideo = {"mp4": "", "ogg": ""};
          }
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
    },
    template: '<div class="preview--slide" data-ng-include="" src="templateURL"></div>'
  }
}]);

/**
 * Directive to insert html for a slide, that is editable.
 * @param ik-slide: the slide.
 * @param ik-width: the width of the slide.
 */
ikApp.directive('ikSlideEditable', ['templateFactory', function($templateFactory) {
  return {
    restrict: 'E',
    scope: {
      ikWidth: '@',
      ikSlide: '='
    },
    link: function(scope, element, attrs) {
      scope.templateURL = '/partials/slide/slide-loading.html';

      // Watch for changes to ikSlide.
      scope.$watch('ikSlide', function (newVal, oldVal) {
        if (!newVal) return;

        if (scope.ikSlide.media_type === 'image') {
          if (scope.ikSlide.media.length > 0) {
            scope.ikSlide.currentImage = scope.ikSlide.media[0].urls.default_landscape;
          }
          else {
            scope.ikSlide.currentImage = '';
          }
        }
        else {
          if (scope.ikSlide.media.length > 0) {
            if (scope.ikSlide.media[0] === undefined) {
              scope.ikSlide.currentVideo = {"mp4": "", "ogg": ""};
            }
            else {
              scope.ikSlide.currentVideo = scope.ikSlide.media[0];

              // Reload video player.
              setTimeout(function () {
                element.find('#videoPlayer').load();
              }, 1000);
            }
          }
          else {
            scope.ikSlide.currentVideo = {"mp4": "", "ogg": ""};
          }
        }

        if (!scope.template || newVal.template !== oldVal.template) {
          scope.templateURL = '/ik-templates/' + scope.ikSlide.template + '/' + scope.ikSlide.template + '-edit.html';
          scope.template = $templateFactory.getTemplate(scope.ikSlide.template);
        }

        if (!scope.theStyle) {
          // Setup the inline styling
          scope.theStyle = {
            width: "" + scope.ikWidth + "px",
            height: "" + parseFloat(scope.template.idealdimensions.height * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px",
            fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px"
          }
        } else {
          // Update fontsize
          scope.theStyle.fontsize =  "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.idealdimensions.width)) + "px";
        }
      }, true);
    },
    templateUrl: '/partials/slide/slide-edit.html'
  }
}]);
