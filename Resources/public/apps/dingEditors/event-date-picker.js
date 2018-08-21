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

function dateDecorator(date) {
  return 'Dato:' + date
}

function headlineChange(scope) {
  const options = scope.slide.options
  const date = options.date

  function setInfoHeader(value) {
    options.infoheader = value
  }

  if (date.text) {
    setInfoHeader(date.text)
  } else if (date.from && date.to) {
    setInfoHeader(dateDecorator(date.from + ' - ' + date.to))
  } else if (date.from) {
    setInfoHeader(dateDecorator(date.from))
  } else {
    setInfoHeader('')
  }
}

angular.module('toolsModule').directive('eventDatePicker', function() {
  return {
    restrict: 'E',
    replace: true,
    scope: {
      slide: '=',
      close: '&',
      template: '@'
    },
    link: function(scope) {
      function change() {
        return headlineChange(scope)
      }
      scope.onFromChange = change
      scope.onToChange = change
      scope.onTextChange = change
    },
    templateUrl:
      '/bundles/kkbding2integration/apps/dingEditors/event-date-picker.html'
  }
})
