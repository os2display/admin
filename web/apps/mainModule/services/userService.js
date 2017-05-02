/**
 * User service.
 */
angular.module('mainModule').service('userService', ['busService', '$http',
  function (busService, $http) {
    'use strict';

    /**
     * Get users event listener.
     */
    busService.$on('userService.getUsers', function requestUser(event, args) {
      $http.get('/api/user')
      .success(function (data) {
        busService.$emit('userService.returnUsers', data);
      })
      .error(function (err) {
        console.log(err);
        busService.$emit('userService.returnUsersError', err);
      });
    });

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
