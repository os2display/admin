/**
 * @file
 * Contains the helper directives.
 */

/**
 * Overrides the contenteditable html5 tag to make the field reflect an ngModel.
 */
ikApp.directive('contenteditable', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ctrl) {

      // From http://stackoverflow.com/questions/4811822/get-a-ranges-start-and-end-offsets-relative-to-its-parent-container/4812022#4812022
      function getCaretCharacterOffsetWithin(element) {
        var caretOffset = 0;
        var doc = element.ownerDocument || element.document;
        var win = doc.defaultView || doc.parentWindow;
        var sel;
        if (typeof win.getSelection != "undefined") {
          var range = win.getSelection().getRangeAt(0);
          var preCaretRange = range.cloneRange();
          preCaretRange.selectNodeContents(element);
          preCaretRange.setEnd(range.endContainer, range.endOffset);
          caretOffset = preCaretRange.toString().length;
        } else if ( (sel = doc.selection) && sel.type != "Control") {
          var textRange = sel.createRange();
          var preCaretTextRange = doc.body.createTextRange();
          preCaretTextRange.moveToElementText(element);
          preCaretTextRange.setEndPoint("EndToEnd", textRange);
          caretOffset = preCaretTextRange.text.length;
        }
        return caretOffset;
      }

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

      // Replace enter, to avoid insertion of html tags in the data field.
      element.on('keydown', function(event) {
        if (event.keyCode == 13) {
          event.preventDefault();

          // Insert extra newline if this is the end of the text.
          // To make sure we get a new line at the end.
          if (getCaretCharacterOffsetWithin(element[0]) == element[0].innerHTML.length) {
            document.execCommand('insertHTML', false, '\r\n');
          }
          document.execCommand('insertHTML', false, '\r\n');
        }
      });
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

