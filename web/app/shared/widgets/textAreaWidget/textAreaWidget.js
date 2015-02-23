/**
 * @file
 * Contains the itkTextAreaWidget module.
 */

/**
 * Setup the module.
 */
(function() {
  var app;
  app = angular.module("itkTextAreaWidget", []);

  /**
   * text-area-widget directive.
   *
   * html paramters:
   *   field: The field to modify.
   *   placeholderText (string): The placeholder text to display.
   */
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
