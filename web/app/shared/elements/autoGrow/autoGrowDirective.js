/**
 * @file
 * Contains the auto-grow directive.
 */

/**
 * HTML attribute for textareas. Makes the textarea grow.
 * Uses jQuery.
 */
angular.module('ikApp').directive('autoGrow', function() {
  'use strict';

  return {
    restrict: 'A',
    scope: {
      fontSize: '@'
    },
    link: function(scope, element) {
      var el = $(element);

      var resizeTextArea = function resizeTextArea(el) {
        el.css('height', '0px');
        var sh = el.prop('scrollHeight');
        var minh = el.css('min-height').replace('px', '');
        el.css('height', Math.max(sh, minh) + 'px');
      };

      element.bind('keyup', function() {
        resizeTextArea(el);
      });

      scope.$watch('fontSize', function(val) {
        if (val) {
          resizeTextArea(el);
        }
      });

      setTimeout(function() {
        resizeTextArea(el);
      }, 100);
    }
  };
});