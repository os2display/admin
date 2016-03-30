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
  app.directive('screenTemplatePickerWidget', ['templateFactory', 'busService',
    function (templateFactory, busService) {
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
              busService.$emit('log.error', {
                'cause': reason,
                'msg': 'Kunne ikke loade templates.'
              });
            }
          );

          /**
           * Set the template for the screen.
           * @param template
           *   The template.
           */
          scope.pickTemplate = function pickTemplate(template) {
            scope.screen.template = angular.copy(template);
          };
        },
        templateUrl: 'app/shared/widgets/screenTemplatePickerWidget/screenTemplatePickerWidget.html?' + window.config.version
      };
    }
  ]);
}).call(this);
