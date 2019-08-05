angular.module('datetimePicker').directive('timePicker', function() {
  return {
    restrict: 'A',
    require: '^ngModel',
    link: function(scope, el) {
      el.datetimepicker({
        datepicker: false,
        format: 'H.i',
        step: 15
      })
    }
  }
})

angular.module('toolsModule').directive('eventTimePicker', function() {
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide: '=',
      close: '&',
      template: '@'
    },
    templateUrl:
      '/bundles/kkbding2integration/apps/dingEditors/event-time-picker.html'
  }
})
