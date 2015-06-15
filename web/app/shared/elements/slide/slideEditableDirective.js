/**
 * @file
 * Contains the directive for an editable slide.
 */

/**
 * Directive to insert html for a slide, that is editable.
 * @param ik-slide: the slide.
 * @param ik-width: the width of the slide.
 */
angular.module('ikApp').directive('ikSlideEditable', ['templateFactory', 'itkLog',
  function (templateFactory, itkLog) {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikWidth: '@',
        ikSlide: '='
      },
      link: function (scope, element, attrs) {
        scope.templateURL = '/app/shared/elements/slide/slide-loading.html?' + window.config.version;

        // Watch for changes to ikSlide.
        scope.$watch('ikSlide', function (newVal, oldVal) {
          if (!newVal) {
            return;
          }

          if (scope.ikSlide.media_type === 'image') {
            if (scope.ikSlide.media.length > 0) {
              scope.ikSlide.currentImage = scope.ikSlide.media[0].urls.default_landscape;
            }
            else {
              scope.ikSlide.currentImage = '';
            }
          }
          else if (scope.ikSlide.media_type === 'video') {
            if (scope.ikSlide.media.length > 0) {
              if (scope.ikSlide.media[0] === undefined) {
                scope.ikSlide.currentVideo = {"mp4": "", "ogg": "", "webm": ""};
              }
              else {
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
            else {
              scope.ikSlide.currentVideo = {"mp4": "", "ogg": "", "webm": ""};
            }
          }

          // Set the currentLogo variable.
          if (scope.ikSlide.logo !== undefined && scope.ikSlide.logo !== null) {
            scope.ikSlide.currentLogo = scope.ikSlide.logo.urls.default_landscape;
          }
          else {
            scope.ikSlide.currentLogo = '';
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
                itkLog.error("Hentning af template fejlede.", reason);
              }
            );
          }

          if (scope.theStyle) {
            // Update fontsize
            scope.theStyle.fontsize = "" + parseFloat(scope.ikSlide.options.fontsize * parseFloat(scope.ikWidth / scope.template.ideal_dimensions.width)) + "px";
          }
        }, true);
      },
      templateUrl: '/app/shared/elements/slide/slide-edit.html?' + window.config.version
    };
  }
]);
