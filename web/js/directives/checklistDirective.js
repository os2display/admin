/**
 * @file
 * Contains the checklist directive
 */


ikApp.directive('ikChecklist', function() {
  "use strict";

  return {
    restrict: 'E',
    replace: false,
    scope: {
      elements: '=',
      selected: '='
    },
    link: function(scope, element, attrs) {
      scope.toggleElement = function toggleElement(el) {
        if (!scope.selected) {
          return;
        }

        var index = -1;

        scope.selected.forEach(function(element, ind) {
          if (element.id === el.id) {
            index = ind;
          }
        });

        if (index > -1) {
          scope.selected.splice(index, 1);
        }
        else {
          scope.selected.push(el);
        }
      };

      scope.elementSelected = function elementSelected(el) {
        if (!scope.selected) {
          return false;
        }

        for (var i = 0; i < scope.selected.length; i++) {
          if (scope.selected[i].id === el.id) {
            return true;
          }
        }

        return false;
      };
    },
    templateUrl: 'partials/directives/checklist-directive.html'
  };
});
