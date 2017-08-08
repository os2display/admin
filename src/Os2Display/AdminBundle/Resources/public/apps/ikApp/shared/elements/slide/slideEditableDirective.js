/**
 * @file
 * Contains the directive for an editable slide.
 */

/**
 * Directive to insert html for a slide, that is editable.
 * @param ik-slide: the slide.
 * @param ik-width: the width of the slide.
 */
angular.module('ikApp').directive('ikSlideEditable', ['templateFactory', 'busService',
  function (templateFactory, busService) {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikWidth: '@',
        ikSlide: '='
      },
      link: function (scope, element, attrs) {
        scope.templateURL = 'bundles/os2displayadmin/apps/ikApp/shared/elements/slide/slide-loading.html?' + window.config.version;

        // Watch for changes to ikSlide.
        scope.$watch('ikSlide', function (newVal, oldVal) {
          if (!newVal) {
            return;
          }

          if (scope.ikSlide.media_type === 'image') {
            scope.ikSlide.currentImage = '';
            if (scope.ikSlide.media.length > 0) {
              scope.ikSlide.currentImage = scope.ikSlide.media[0].urls.default_landscape;
            }
          }
          else if (scope.ikSlide.media_type === 'video') {
            scope.ikSlide.currentVideo = {"mp4": "", "ogg": "", "webm": ""};
            if (scope.ikSlide.media.length > 0 && scope.ikSlide.media[0] !== undefined) {
              if (scope.ikSlide.media.length > 0 && scope.ikSlide.media[0].provider_metadata.length > 0) {
                // Set current video variable to path to video files.
                scope.ikSlide.currentVideo = {};
                if (scope.ikSlide.media[0].provider_metadata[0]) {
                  scope.ikSlide.currentVideo.mp4 = scope.ikSlide.media[0].provider_metadata[0].reference;
                }
                if (scope.ikSlide.media[0].provider_metadata[1]) {
                  scope.ikSlide.currentVideo.ogg = scope.ikSlide.media[0].provider_metadata[1].reference;
                }
                if (scope.ikSlide.media[0].provider_metadata[2]) {
                  scope.ikSlide.currentVideo.webm = scope.ikSlide.media[0].provider_metadata[2].reference;
                }
              }

              // Reload video player.
              setTimeout(function () {
                element.find('#videoPlayer').load();
              }, 1000);
            }
          }

          // Set the currentLogo variable.
          scope.ikSlide.currentLogo = '';
          if (scope.ikSlide.logo !== undefined && scope.ikSlide.logo !== null) {
            scope.ikSlide.currentLogo = scope.ikSlide.logo.urls.default_landscape;
          }

          if (!scope.template || newVal.template !== oldVal.template) {
            templateFactory.getSlideTemplate(scope.ikSlide.template).then(
              function success(data) {
                scope.template = data;
                scope.templateURL = scope.template.paths.edit;

                // Setup the inline styling
                scope.theStyle = {
                  width: "" + scope.ikWidth + "px",
                  height: "" + parseFloat(scope.template.ideal_dimensions.height * parseFloat(scope.ikWidth / scope.template.ideal_dimensions.width)) + "px",
                  fontsize: "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.ideal_dimensions.width)) + "px"
                };
              },
              function error(reason) {
                busService.$emit('log.error', {
                  'cause': reason,
                  'msg': 'Hentning af templates fejlede.'
                });
              }
            );
          }

          if (scope.theStyle) {
            // Update font size
            scope.theStyle.fontsize = "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.ideal_dimensions.width)) + "px";
          }
        }, true);
      },
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/shared/elements/slide/slide-edit.html?' + window.config.version
    };
  }
]);
