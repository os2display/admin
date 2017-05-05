/**
 * @file
 * Contains box component.
 * See styleguide: modules/content-list.
 */

/**
 * html parameters:
 *   items: The items to render in list.
 */
angular.module('styleguideComponentsApp').directive('contentListItem', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      item: '='
    },
    template:
      '<div class="content-list-item">' +
        '<span class="content-list-item--link">' +
          '<a href="{{ item.url }}" title="{{ item.title }}" ng-if="item.url">' +
            '{{ item.title }}' +
          '</a>' +
          '<span class="button-text {{ data.class }}" ng-if="!item.url" ng-click="item.click(item.entity)">' +
            '{{ item.title }}' +
          '</span>' +
        '</span>' +
        '<panel-menu items="item.actions" ng-if="item.actions"></panel-menu>' +
      '</div>'
  };
});
