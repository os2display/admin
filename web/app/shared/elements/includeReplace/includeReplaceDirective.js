/**
 * @file
 * Contains the include-replace directive.
 */

/**
 * HTML attribute to replace the ng-include div.
 */
angular.module('ikApp').directive('includeReplace', function () {
  'use strict';

  return {
    require: 'ngInclude',
    restrict: 'A',
    link: function (scope, el) {
      el.replaceWith(el.children());
    }
  };
});
