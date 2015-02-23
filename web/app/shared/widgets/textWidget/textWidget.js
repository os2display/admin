/**
 * @file
 * Contains the itkTextWidget module.
 */

/**
 * Setup the module.
 */
(function() {
  var app;
  app = angular.module("itkTextWidget", []);

  /**
   * text-widget directive.
   *
   * html paramters:
   *   field: The field to modify.
   *   placeholderText (string): The placeholder text to display.
   */
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
