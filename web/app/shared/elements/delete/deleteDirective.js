/**
 * @file
 * Add delete button
 */

/**
 * Channel preview directive. Displays the channel preview.
 * Has a play button.
 * When pressing the channel, but not the play button, redirect to the channel editor.
 */
angular.module('ikApp').directive('ikDelete', ['$http', '$rootScope', 'itkLogFactory', 'configuration',
  function($http, $rootScope, itkLogFactory, configuration) {
  'use strict';

  return {
    restrict: 'E',
    replace: false,
    scope: {
      id: '@',
      type: '@'
    },
    link: function(scope) {
      // Handle clicks on numbers.
      scope.remove = function () {
        var result = window.confirm('Er du sikker på du vil slette dette? Handlingen kan ikke fortrydes.');
        if (result === true) {
          $http.delete('/api/' + scope.type + '/' + scope.id)
            .success(function() {
              itkLogFactory.info('Sletning lykkedes.');
              $rootScope.$broadcast(scope.type + '-deleted', {});
            })
            .error(function(reason) {
              itkLogFactory.error('Sletning lykkes ikke!', reason);
            });
        }
      };
    },
    templateUrl: 'app/shared/elements/delete/delete.html?' + configuration.version
  };
}]);
