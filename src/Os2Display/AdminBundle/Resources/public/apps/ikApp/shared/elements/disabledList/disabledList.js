/**
 * @file
 * Contains the DisabledList directive
 */

/**
 * DisabledList directive.
 *
 */
angular.module('ikApp').directive('ikDisabledList', [
  function () {
    'use strict';

    return {
      restrict: 'E',
      replace: false,
      scope: {
        elements: '='
      },
      templateUrl: 'bundles/os2displayadmin/apps/ikApp/shared/elements/disabledList/disabled-list.html?' + window.config.version
    };
  }
]);
