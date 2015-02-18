(function() {
  var app;
  app = angular.module("itkScreenTemplatePickerWidget", []);

  app.directive('screenTemplatePickerWidget', ['templateFactory',
    function(templateFactory) {
      return {
        restrict: 'E',
        scope: {
          screen: '='
        },
        replace: true,
        link: function(scope, element, attrs) {
          scope.templates = [];
          templateFactory.getScreenTemplates().then(function(data) {
            scope.templates = data;
          });

          scope.pickTemplate = function pickTemplate(template) {
            scope.screen.template = template;
          };
        },
        templateUrl: 'app/shared/widgets/screenTemplatePickerWidget/screenTemplatePickerWidget.html'
      };
    }
  ]);
}).call(this);
