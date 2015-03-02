/**
 * @file
 * Contains the itkControlPanel module.
 */

/**
 * Setup the module.
 */
(function() {
  var app;
  app = angular.module("itkControlPanel", []);

  /**
   * control-panel directive.
   *
   * html parameters:
   *   template (string): The control panel template to render.
   *   data (object): The data object to manipulate in the control panel.
   *   display (boolean): Should the control panel be visible?
   *   saveAction (function): The function to call on a saveAction.
   *   region (integer): The region (id) to manipulate.
   */
  app.directive('controlPanel',
    function() {
      return {
        restrict: 'E',
        scope: {
          template: '=',
          data: '=',
          display: '=',
          saveAction: '&',
          region: '='
        },
        replace: true,
        link: function(scope, element, attrs) {
          // Which control panel tab is selected?
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
