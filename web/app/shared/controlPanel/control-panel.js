(function() {
  var app;
  app = angular.module("itkControlPanel", []);

  app.directive('controlPanel',
    function() {
      return {
        restrict: 'E',
        scope: {
          template: '=',
          data: '=',
          displayTool: '@'
        },
        replace: true,
        link: function(scope, element, attrs) {
          scope.selectedTab = null;

          /**
           * Get the template for the control panel.
           * @returns string
           *   Path to the template.
           */
          scope.getContent = function getContent() {
            return scope.template;
          };

          /**
           * Click a tab.
           * @param tab
           *   Name of the tab to display and which nav to show active.
           */
          scope.clickTab = function clickNav(tab) {
            scope.selectedTab = tab;
          };
        },
        template: '<div data-ng-include="getContent()"></div>'
      };
    }
  );
}).call(this);
