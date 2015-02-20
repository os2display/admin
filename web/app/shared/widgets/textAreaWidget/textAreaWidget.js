(function() {
  var app;
  app = angular.module("itkTextAreaWidget", []);

  app.directive('textAreaWidget',
    function() {
      return {
        restrict: 'E',
        scope: {
          field: '=',
          placeholderText: '@'
        },
        replace: true,
        template: '<textarea class="cpw--textarea-input" data-ng-model="field" placeholder="{{placeholderText}}"></textarea>'
      };
    }
  );
}).call(this);
