angular.module('templateModule').directive('baseEditor', [function () {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        slide: '=',
        template: '@',
        close: '&'
      },
      templateUrl: function(elem, attrs) {
        return attrs.template;
      }
    };
  }
]);
