/**
 * @file
 * Contains channel share directives.
 */

/**
 *
 */
ikApp.directive('ikChannelShare',
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
      template: '<span data-ng-click="clickShare()">Del</span>'
    }
  }
);
