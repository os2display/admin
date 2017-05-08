/**
 * @file
 * Contains panel-menu component.
 * See styleguide: modules/panel-menu.
 */

/**
 * html parameters:
 *   items: The item to render.
 */
angular.module('styleguideComponentsApp').directive('panelMenu', ['$document', function ($document) {
  return {
    restrict: 'E',
    replace: true,
    scope: {
      items: '='
    },
    link: function (scope) {
      scope.menuOpen = false;

      function clickHandler() {
        scope.menuOpen = false;
      }

      scope.showMenu = function () {
        $document.off('click', clickHandler);
        $document.on('click', clickHandler);

        scope.menuOpen = true;
      };

      /**
       * onDestroy.
       */
      scope.$on('$destroy', function () {
        $document.off('click', clickHandler);
      })
    },
    template:
      '<div>' +
        '<span class="content-list-item--icon" ng-click="showMenu()">' +
          '<i class="icon-default material-icons">more_vert</i>' +
        '</span>' +
        '<div class="panel-menu is-positioned" ng-class="{\'is-hidden\': !menuOpen}">' +
          '<box>' +
            '<content-list items="items"></content-list>' +
          '</box>' +
        '</div>' +
      '</div>'
  }
}]);