/**
 * @file
 * Contains box component.
 * See styleguide: modules/button-icon-link.
 */

/**
 * html parameters:
 *   button-link: The link text.
 *   icon: The icon.
 *   click: The click function.
 */
angular.module('styleguideComponentsModule').directive('buttonIconLink', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      buttonLink:'@',
      icon: '@',
      click: '&'
    },
    template:
      '<div class="button-icon-link has-spacing-after" ng-click="click()">' +
        '<i class="icon-rounded material-icons">{{ icon }}</i>' +
        '<span class="button-text has-spacing-before">{{ buttonLink }}</span>' +
      '</div>'
  };
});
