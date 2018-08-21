angular.module('datetimePicker').directive('datePicker', () => ({
  restrict: 'A',
  require: '^ngModel',
  link: (_, el) => {
    el.datetimepicker({
      timepicker: false,
      format: 'd.m.Y'
    })
  }
}))

const dateDecorator = date => `Dato: ${date}`

const headlineChange = scope => {
  const options = scope.slide.options
  const { text, from, to } = options.date

  const setInfoHeader = (value = '') => options.infoheader = value

  if (text) setInfoHeader(text)
  else if (from && to) setInfoHeader(dateDecorator(`${from} - ${to}`))
  else if (from) setInfoHeader(dateDecorator(from))
  else setInfoHeader()
}

angular.module('toolsModule').directive('eventDatePicker', () => ({
  restrict: 'E',
  replace: true,
  scope: {
    slide: '=',
    close: '&',
    template: '@'
  },
  link: scope => {
    const change = () => headlineChange(scope)
    scope.onFromChange = change
    scope.onToChange = change
    scope.onTextChange = change
  },
  templateUrl:
    '/bundles/kkbding2integration/apps/dingEditors/event-date-picker.html'
}))
