/**
 * @file
 * Add delete button
 */

/**
 * Channel preview directive. Displays the channel preview.
 * Has a play button.
 * When pressing the channel, but not the play button, redirect to the channel editor.
 */
angular.module('ikApp').directive('ikActionReloadScreen', ['$http', 'busService',
  function ($http, busService) {
    'use strict';

    return {
      restrict: 'E',
      replace: false,
      scope: {
        id: '@'
      },
      link: function (scope) {
        scope.reloadScreen = function () {
          var result = window.confirm('Er du sikker på at du vil genindlæse skærmen?');
          if (result === true) {
            $http.post('/api/screen/' + scope.id + '/reload')
              .success(function () {
                busService.$emit('log.info', {
                  'msg': 'Genindlæsning lykkedes.',
                  'timeout': 3000
                });
              })
              .error(function (reason) {
                busService.$emit('log.error', {
                  'cause': reason,
                  'msg': 'Genindlæsning lykkedes ikke! Dette kan skyldes at skærmen ikke er forbundet.'
                });
              });
          }
        };
      },
      templateUrl: 'app/shared/elements/actionReloadScreen/action-reload-screen.html?' + window.config.version
    };
  }
]);
