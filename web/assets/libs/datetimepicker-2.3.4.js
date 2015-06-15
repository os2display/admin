/**
 * @file
 * Contains an angular wrapper for the datetime-picker jquery plugin.
 */

/**
 * Angular wrapper for jquery.datetimepicker.js.
 * For v2.3.4
 * Only a one way binding atm.
 */
angular.module('datetimePicker', [])
.directive('datetimePicker', function () {
  return {
    restrict: 'A',
    require: '^ngModel',
    link: function(scope, el, attrs, ctrl) {
      var dateFormat = 'DD/MM/YYYY HH:mm';
      el.datetimepicker({
        lang: 'da',
        format: 'd/m/Y H:i'
      });

      ctrl.$formatters.unshift(function (modelValue) {
        if (!modelValue) return "";

        return moment(modelValue * 1000).format(dateFormat);
      });

      ctrl.$parsers.unshift(function (viewValue) {
        var date = moment(viewValue, dateFormat);

        return (date && date.isValid() && date.year() > 1950 ) ? date.unix() : "";
      });
    }
  }
});

angular.module('datetimePicker')
.directive('timePicker',
  function () {
    return {
      restrict: 'A',
      require: '^ngModel',
      link: function (scope, el) {
        el.datetimepicker({
          datepicker:false,
          format:'H:i'
        });
      }
    }
  }
);
