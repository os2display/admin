/**
 * @file
 * Contains channel share directives.
 */

/**
 *
 */
angular.module('ikApp').directive('ikChannelShare',
  function() {
    return {
      restrict: 'E',
      scope: {
        ikChannel: '='
      },
      link: function(scope, element, attrs) {
        scope.clickShare = function() {
          scope.$emit('ikChannelShare.clickShare', scope.ikChannel);
        }
      },
      templateUrl: 'app/shared/elements/channelShare/channel-share.html'
    }
  }
);
