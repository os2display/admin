/**
 * @file
 * Contains panel-menu component.
 * See styleguide: modules/panel-menu.
 */

/**
 * html parameters:
 *   items: The item to render.
 */
angular.module('styleguideComponentsApp').directive('panelMenu', function(){
  return {
    restrict: 'E',
    replace: true,
    scope: {
      items: '='
    },
    link: function (scope) {
      scope.showMenu = false;

      scope.toggle = function () {
        scope.showMenu = !scope.showMenu;
      }
    },
    template:
      '<div>' +
        '<span class="content-list-item--icon" ng-click="toggle()">' +
          '<i class="icon-default material-icons">more_vert</i>' +
        '</span>' +
        '<div class="panel-menu is-positioned" ng-class="{\'is-hidden\': !showMenu}" ng-click="showMenu = false">' +
          '<box>' +
            '<content-list items="items"></content-list>' +
          '</box>' +
        '</div>' +
      '</div>'
  }
});