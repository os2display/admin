/**
 * @file
 * Contains the helper directives.
 */

/**
 * HTML attribute to replace the ng-include div.
 */
ikApp.directive('includeReplace', function () {
  return {
    require: 'ngInclude',
    restrict: 'A',
    link: function (scope, el, attrs) {
      el.replaceWith(el.children());
    }
  };
});

/**
 * HTML attribute for textareas. Makes the textarea grow.
 * Uses jQuery.
 */
ikApp.directive('autoGrow', function() {
  return {
    restrict: 'A',
    scope: {
      fontSize: '@'
    },
    link: function(scope, element, attr) {
      var el = $(element);

      var resizeTextArea = function resizeTextArea(el) {
        el.css("height", "0px");
        var sh = el.prop("scrollHeight");
        var minh = el.css("min-height").replace("px", "");
        el.css("height", Math.max(sh, minh) + "px");
      }

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
  }
});