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
  app.directive('screenTemplatePickerWidget', ['templateFactory',
    function (templateFactory) {
      return {
        restrict: 'E',
        scope: {
          screen: '='
        },
        replace: true,
        link: function (scope, element, attrs) {
          scope.templates = [];
          templateFactory.getScreenTemplates().then(function (data) {
            scope.templates = data;
          });

          /**
           * Set the template for the screen.
           * @param template
           *   The template.
           */
          scope.pickTemplate = function pickTemplate(template) {
            scope.screen.template = template;
          };
        },
        templateUrl: 'app/shared/widgets/screenTemplatePickerWidget/screenTemplatePickerWidget.html'
      };
    }
  ]);
}).call(this);
