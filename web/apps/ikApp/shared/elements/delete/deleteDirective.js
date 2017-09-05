/**
 * @file
 * Add delete button
 */

/**
 * Channel preview directive. Displays the channel preview.
 * Has a play button.
 * When pressing the channel, but not the play button, redirect to the channel editor.
 */
angular.module('ikApp').directive('ikDelete', ['$http', '$rootScope', 'busService',
  function ($http, $rootScope, busService) {
    'use strict';

    return {
      restrict: 'E',
      replace: false,
      scope: {
        id: '@',
        type: '@'
      },
      link: function (scope) {
        // Handle clicks on numbers.
        scope.remove = function () {
          var result = window.confirm('Er du sikker på du vil slette dette? Handlingen kan ikke fortrydes.');
          if (result === true) {
            $http.delete('/api/' + scope.type + '/' + scope.id)
              .success(function () {
                busService.$emit('log.info', {
                  'msg': 'Sletning lykkes.',
                  'timeout': 3000
                });
                $rootScope.$broadcast(scope.type + '-deleted', {});
              })
              .error(function (reason) {
                busService.$emit('log.error', {
                  'cause': reason,
                  'msg': 'Sletning lykkes ikke!'
                });
              });
          }
        };
      },
      templateUrl: 'apps/ikApp/shared/elements/delete/delete.html?' + window.config.version
    };
  }
]);
