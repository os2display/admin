(function() {
  var app;
  app = angular.module("itkTextWidget", []);

  app.directive('textWidget',
    function() {
      return {
        restrict: 'E',
        scope: {
          field: '=',
          placeholderText: '@'
        },
        replace: true,
        template: '<input type="text" class="cpw--text-input" placeholder="{{placeholderText}}" data-ng-model="field">'
      };
    }
  );
}).call(this);
