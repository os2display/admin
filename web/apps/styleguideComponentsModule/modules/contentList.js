/**
 * @file
 * Contains box component.
 * See styleguide: modules/content-list.
 */

/**
 * html parameters:
 *   items: The items to render in list.
 */
angular.module('styleguideComponentsModule').directive('contentList', function () {
  return {
    restrict: 'E',
    replace: true,
    scope: {
      items: '=',
      max: '=',
      filter: '=',
      order: '='
    },
    template:
      '<div class="content-list">' +
        '<content-list-item ng-repeat="item in items | filter: filter | orderBy: order | limitTo: max" item="item"></content-list-item>' +
      '</div>'
  };
});
