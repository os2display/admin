/**
 * @file
 * Contains box component.
 * See styleguide: modules/box.
 */

/**
 * html parameters:
 *   heading: The heading of the box.
 */
angular.module('styleguideComponentsModule').directive('box', function(){
  return {
    restrict: 'E',
    transclude: true,
    replace: true,
    scope: {
      heading:'@'
    },
    template:
      '<article class="box">' +
        '<h2 class="heading-has-spacing-after" ng-if="heading">{{ heading }}</h2>' +
        '<div class="box--inner" ng-transclude>' +
        '</div>' +
      '</article>'
  };
});
