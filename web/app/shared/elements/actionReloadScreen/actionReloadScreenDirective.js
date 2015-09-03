/**
 * @file
 * Add delete button
 */

/**
 * Channel preview directive. Displays the channel preview.
 * Has a play button.
 * When pressing the channel, but not the play button, redirect to the channel editor.
 */
angular.module('ikApp').directive('ikActionReloadScreen', ['$http', 'itkLog',
  function ($http, itkLog) {
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
            $http.post('/api/screen/reload/' + scope.id)
              .success(function () {
                itkLog.info('Genindlæsning lykkedes.');
              })
              .error(function (reason) {
                itkLog.error('Genindlæsning lykkedes ikke!', reason);
              });
          }
        };
      },
      templateUrl: 'app/shared/elements/actionReloadScreen/action-reload-screen.html?' + window.config.version
    };
  }
]);
