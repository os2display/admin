angular.module('datetimePicker').directive('datePicker', function() {
  return {
    restrict: 'A',
    require: '^ngModel',
    link: function(_, el) {
      el.datetimepicker({
        timepicker: false,
        format: 'd.m.Y'
      })
    }
  }
})

angular.module('toolsModule').directive('eventDatePicker', function() {
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide: '=',
      close: '&',
      template: '@'
    },
    templateUrl:
      '/bundles/kkbding2integration/apps/dingEditors/event-date-picker.html'
  }
})
