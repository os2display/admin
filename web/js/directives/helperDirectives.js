/**
 * Overrides the contenteditable html5 tag to make the field reflect an ngModel.
 */
ikApp.directive('contenteditable', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ctrl) {
      // view -> model
      element.bind('blur', function() {
        scope.$apply(function() {
          ctrl.$setViewValue(element.html());
        });
      });

      // model -> view
      ctrl.$render = function() {
        element.html(ctrl.$viewValue);
      };

      // load init value.
      ctrl.$render();

      if (element[0].tagName == 'PRE') {
        // Replace enter, to avoid insertion of html tags in the data field.
        element.on('keydown', function(event) {
          if (event.keyCode == 13) {
            event.preventDefault();
            document.execCommand('insertText', false, '\r\n');
          }
        });
      } else {
        element.on('keydown', function(event) {
          if (event.keyCode == 13) {
            event.preventDefault();
          }
        });
      }
    }
  };
});

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

