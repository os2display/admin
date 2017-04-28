/**
 * @file
 * Contains box component.
 * See styleguide: modules/content-list.
 */

/**
 * html parameters:
 *   items: The items to render in list.
 */
angular.module('styleguideComponentsApp').directive('contentList', function(){
  return {
    restrict: 'E',
    transclude: true,
    replace: true,
    scope: {
      items:'@'
    },
    template:
    '<div class="content-list">' +
      '<div class="content-list-item" ng-repeat="item in items">' +
        '<a href="{{ item.url }}" title="List item 1">' +
          '{{ item.title }}' +
        '</a>' +
      '</div>' +
    '</div>'
  };
});
