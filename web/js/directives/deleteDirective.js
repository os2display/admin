/**
 * @file
 * Add delete button
 */

/**
 * Channel preview directive. Displays the channel preview.
 * Has a play button.
 * When pressing the channel, but not the play button, redirect to the channel editor.
 */
ikApp.directive('ikDelete', ['$http', '$rootScope', function($http, $rootScope) {
  "use strict";

  return {
    restrict: 'E',
    replace: false,
    scope: {
      id: '@',
      type: '@'
    },
    link: function(scope, element, attrs) {
      // Handle clicks on numbers.
      scope.remove = function () {
        var result = window.confirm("Er du sikre på at du vil udføre sletningne!");
        if (result === true) {
          $http.delete('/api/' + scope.type + '/' + scope.id)
            .success(function(data) {
              $rootScope.$broadcast(scope.type + '-deleted', {});
            })
            .error(function() {
              alert('Sletning lykkes ikke!');
            });
        }
      };
    },
    templateUrl: 'partials/directives/delete-directive.html'
  };
}]);
