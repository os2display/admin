(function() {
  var app;
  app = angular.module("itkControlPanel", []);

  app.directive('controlPanel',
    function() {
      return {
        restrict: 'E',
        scope: {
          template: '=',
          data: '='
        },
        replace: true,
        link: function(scope, element, attrs) {
          scope.getContent = function getContent() {
            return scope.template;
          }
        },
        template: '<div data-ng-include="getContent()"></div>'
      };
    }
  );
}).call(this);
