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
          '<a href="{{ item.url }}" title="{{ item.title }}">' +
            '{{ item.title }}' +
          '</a>' +
        '</span>' +
      '</div>'
  };
});
