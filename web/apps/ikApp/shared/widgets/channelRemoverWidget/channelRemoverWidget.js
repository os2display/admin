/**
 * @file
 * Contains the itkChannelRemoveWidget module.
 */

/**
 * Setup the module.
 */
(function () {
  'use strict';

  var app;
  app = angular.module("itkChannelRemoverWidget", []);

  /**
   * channel-remover-widget directive.
   *
   * html parameters:
   *   screen (object): the screen to modify.
   *   region (integer): the region of the screen to modify.
   */
  app.directive('channelRemoverWidget', [
    function () {
      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'apps/ikApp/shared/widgets/channelRemoverWidget/channel-remover-widget.html?' + window.config.version,
        scope: {
          screen: '=',
          region: '='
        },
        link: function (scope) {
          scope.search_text = '';

          /**
           * Get the search object for the filter.
           * @returns {{title: string}}
           */
          scope.getSearch = function getSearch() {
            return {
              "title": scope.search_text
            };
          };

          /**
           * Removing a channel from a screen region.
           * @param channel
           *   Channel to remove from the screen region.
           */
          scope.removeChannel = function removeChannel(channel) {
            var element;
            for (var i = 0; i < scope.screen.channel_screen_regions.length; i++) {
              element = scope.screen.channel_screen_regions[i];
              if (element.shared_channel === undefined && element.channel.id === channel.id && element.region === scope.region) {
                scope.screen.channel_screen_regions.splice(i, 1);
              }
              else if (element.shared_channel && element.shared_channel.unique_id === channel.unique_id && element.region === scope.region) {
                scope.screen.channel_screen_regions.splice(i, 1);
              }
            }
          };
        }
      };
    }
  ]);
}).call(this);
