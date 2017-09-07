/**
 * @file
 * Add delete button
 */

/**
 * Channel preview directive. Displays the channel preview.
 * Has a play button.
 * When pressing the channel, but not the play button, redirect to the channel editor.
 */
angular.module('ikApp').directive('ikActionsMenu', [
  function () {
    'use strict';

    return {
      restrict: 'E',
      replace: false,
      transclude: true,
      scope: {
      },
      link: function (scope) {
        scope.menuOpen = false;

        scope.toggleMenu = function toggleMenu() {
          scope.menuOpen = !scope.menuOpen;
        }
      },
      templateUrl: 'apps/ikApp/shared/elements/actionsMenu/actions-menu.html?' + window.config.version
    };
  }
]);
