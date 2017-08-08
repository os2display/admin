/**
 * @file
 * Contains the itkRegionPreviewWidget module.
 */

/**
 * Setup the module.
 */
(function () {
  'use strict';

  var app;
  app = angular.module("itkRegionPreviewWidget", []);

  /**
   * region-preview-widget directive.
   *
   * html paramters:
   *   region (integer): The region to modify.
   *   screen (object): The screen to modify.
   */
  app.directive('regionPreviewWidget', [
    function () {
      return {
        restrict: 'E',
        scope: {
          region: '=',
          screen: '=',
          width: '='
        },
        replace: false,
        templateUrl: 'bundles/os2displayadmin/apps/ikApp/shared/widgets/regionPreviewWidget/region-preview-widget.html?' + window.config.version,
        link: function (scope) {
          scope.getNumberOfChannels = function getNumberOfChannels() {
            var n = 0;

            for (var i = 0; i < scope.screen.channel_screen_regions.length; i++) {
              if (scope.screen.channel_screen_regions[i].region === scope.region) {
                n++
              }
            }

            return n;
          };
        }
      };
    }
  ]);
}).call(this);
