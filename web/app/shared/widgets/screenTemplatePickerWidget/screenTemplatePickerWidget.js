/**
 * @file
 * Contains the itkScreenTemplatePickerWidget module.
 */

/**
 * Setup the module.
 */
(function () {
  'use strict';

  var app;
  app = angular.module("itkScreenTemplatePickerWidget", []);

  /**
   * screen-template-picker-widget directive.
   *
   * html-parameters:
   *   screen (object): the screen to modify.
   */
  app.directive('screenTemplatePickerWidget', ['templateFactory', 'itkLogFactory', 'configuration',
    function (templateFactory, itkLogFactory, configuration) {
      return {
        restrict: 'E',
        scope: {
          screen: '='
        },
        replace: true,
        link: function (scope) {
          scope.templates = [];
          templateFactory.getScreenTemplates().then(
            function success(data) {
              scope.templates = data;
            },
            function error(reason) {
              itkLogFactory.error("Kunne ikke loade templates", reason);
            }
          );

          /**
           * Set the template for the screen.
           * @param template
           *   The template.
           */
          scope.pickTemplate = function pickTemplate(template) {
            scope.screen.template = template;
          };
        },
        templateUrl: 'app/shared/widgets/screenTemplatePickerWidget/screenTemplatePickerWidget.html?' + configuration.version
      };
    }
  ]);
}).call(this);
