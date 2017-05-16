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

      /**
       * Click handler.
       *
       * @param event
       */
      function clickHandler(event) {
        scope.$apply(function () {
          scope.menuOpen = false;

          document.removeEventListener('click', clickHandler);
        });
      }

      /**
       * Toggle menu.
       */
      scope.toggleMenu = function () {
        scope.menuOpen = !scope.menuOpen;

        // Register click handler after timeout.
        setTimeout(function () {
          if (scope.menuOpen) {
            document.addEventListener('click', clickHandler);
          }
        });
      };
    },
    template:
      '<div>' +
        '<span class="content-list-item--icon" ng-click="toggleMenu()">' +
          '<i class="icon-default material-icons">more_vert</i>' +
        '</span>' +
        '<div class="panel-menu is-positioned" ng-class="{\'is-hidden\': !menuOpen}">' +
          '<box>' +
            '<content-list items="items" ng-click="menuOpen = false"></content-list>' +
          '</box>' +
        '</div>' +
      '</div>'
  }
}]);