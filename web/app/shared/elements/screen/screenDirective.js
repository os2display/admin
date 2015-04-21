/**
 * @file
 * Contains screen directives.
 */

/**
 * Directive to insert a screen.
 */
angular.module('ikApp').directive('ikScreen', [
  function () {
    'use strict';

    return {
      restrict: 'E',
      scope: {
        ikScreen: '='
      },
      link: function (scope, element, attrs) {
        // Observe for changes to the ikScreen attribute.
        attrs.$observe('ikScreen', function (val) {
          if (!val) {
            return;
          }

          // Set the style of the screen.
          if (scope.ikScreen.template.orientation === 'landscape') {
            scope.style = {
              "width": "300px",
              "height": "168.5px"
            };
          }
          else {
            scope.style = {
              "height": "300px",
              "width": "168.5px"
            };
          }
        });
      },
      templateUrl: 'app/shared/elements/screen/screen-template.html'
    };
  }
]);

