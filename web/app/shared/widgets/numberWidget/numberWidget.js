(function() {
  var app;
  app = angular.module("itkNumberWidget", []);

  app.directive('numberWidget',
    function() {
      return {
        restrict: 'E',
        scope: {
          field: '=',
          placeholderText: '@'
        },
        replace: true,
        template: '<input type="number" class="cpw--text-input" placeholder="{{placeholderText}}" data-ng-model="field">'
      };
    }
  );
}).call(this);
