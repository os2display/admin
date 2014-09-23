/**
 * @file
 * Contains an angular wrapper for the datetime-picker jquery plugin.
 */

/**
 * Angular wrapper for jquery.datetimepicker.js.
 * Only a one way binding atm.
 */
angular.module('datetimePicker', [])
.directive('datetimePicker', function() {
  return {
    restrict: 'A',
    require: 'ngModel',
    link: function(scope, el, attrs, ngModel) {
      el.datetimepicker({
        lang: 'da',
        onChangeDateTime: function(dp, $input) {
          scope.$apply(function() {
            ngModel.$setViewValue($input.val());
          });
        }
      });
    }
  }
});