/**
 * @file
 * Contains channel share directives.
 */

/**
 * channel-share directive.
 *
 * Enables sharing a channel.
 *
 * html-parameters
 *   ikChannel (object): Channel to share.
 */
angular.module('ikApp').directive('ikChannelShare', ['configuration',
  function (configuration) {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikChannel: '='
      },
      link: function (scope) {
        scope.clickShare = function () {
          scope.$emit('ikChannelShare.clickShare', scope.ikChannel);
        };
      },
      templateUrl: 'app/shared/elements/channelShare/channel-share.html?' + configuration.version
    };
  }
]);
