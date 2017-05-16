/**
 * User service.
 */
angular.module('mainModule').service('userService', [
  'busService', '$http', '$q',
  function (busService, $http, $q) {
    'use strict';

    // Get
    var currentUser = OS2DISPLAY_CURRENT_USER;

    /**
     * Get current user promise.
     *
     * @return {HttpPromise}
     */
    var getCurrentUser = function getCurrentUser() {
      var deferred = $q.defer();

      $http.get('/api/user/current')
      .success(function (data) {
        currentUser = data;
        deferred.resolve(currentUser);
      })
      .error(function (response) {
        deferred.reject(response);
      });

      //deferred.resolve(currentUser);

      return deferred.promise;
    };

    /**
     * Get current user event listener.
     */
    busService.$on('userService.getCurrentUser', function requestUser(event, args) {
      getCurrentUser().then(
        function (user) {
          busService.$emit('userService.returnCurrentUser', user);
        },
        function (err) {
          busService.$emit('log.error', {
            'cause': err,
            'msg': 'Bruger kunne ikke hentes'
          });
        }
      )
    });

    this.getCurrentUser = getCurrentUser;
  }
]);
