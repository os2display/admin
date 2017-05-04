/**
 * User service.
 */
angular.module('mainModule').service('userService', ['busService', '$http',
  function (busService, $http) {
    'use strict';

    /**
     * Get current user event listener.
     */
    busService.$on('userService.getCurrentUser', function requestUser(event, args) {
      $http.get('/api/user/current')
        .success(function (data) {
          busService.$emit('userService.returnCurrentUser', data);
        })
        .error(function (response) {
          busService.$emit('log.error', {
            'cause': response,
            'msg': 'Bruger kunne ikke hentes'
          });
        });
    });
  }
]);
