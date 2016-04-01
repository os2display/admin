/**
 * @file
 * Contains the user factory.
 */

/**
 * User factory.
 */
angular.module('mainModule').service('userService', ['busService', '$http',
  function (busService, $http) {
    'use strict';

    var user;

    busService.$on('userService.requestUser', function requestUser(event, args) {
      if (user === undefined) {
        $http.get('/api/user')
          .success(function (data) {
            user = data;
            busService.$emit('userService.returnUser', user);
          })
          .error(function (response) {
            busService.$emit('log.error', {
              'cause': response,
              'msg': 'Bruger kunne ikke hentes'
            });
          });
      }
      else {
        busService.$emit('userService.returnUser', user);
      }
    });
  }
]);
