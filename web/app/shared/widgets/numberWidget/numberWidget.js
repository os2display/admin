/**
 * @file
 * Contains the itkNumberWidget module.
 */

/**
 * Setup the module.
 */
(function () {
  'use strict';

  var app;
  app = angular.module("itkNumberWidget", []);

  /**
   * number-widget directive.
   *
   * html parameters:
   *   field: The field to modify.
   *   placeholderText (string): The placeholder text to display.
   */
  app.directive('numberWidget',
    function () {
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
