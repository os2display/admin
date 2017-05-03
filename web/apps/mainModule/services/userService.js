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
        busService.$emit('userService.returnUsersError', err);
      });
    });

    /**
     * Get user event listener.
     */
    busService.$on('userService.getUser', function requestUser(event, args) {
      $http.get('/api/user/' + args.id)
      .success(function (data) {
        busService.$emit('userService.returnUser', data);
      })
      .error(function (err) {
        busService.$emit('userService.returnUserError', err);
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

    /**
     * Create user event listener.
     */
    busService.$on('userService.createUser', function requestUser(event, args) {
      $http.post('/api/user', args)
      .success(function (data) {
        busService.$emit('userService.returnCreateUser', data);
      })
      .error(function (err) {
        busService.$emit('userService.returnCreateUserError', err);
      });
    });

    /**
     * Update user event listener.
     */
    busService.$on('userService.updateUser', function requestUser(event, args) {
      $http.put('/api/user/' + args.id, args)
      .success(function (data) {
        busService.$emit('userService.returnUpdateUser', data);
      })
      .error(function (err) {
        busService.$emit('userService.returnUpdateUserError', err);
      });
    });

    /**
     * Delete user event listener.
     */
    busService.$on('userService.deleteUser', function requestUser(event, args) {
      $http.delete('/api/user/' + args.id)
      .success(function (data) {
        busService.$emit('userService.returnDeleteUser', data);
      })
      .error(function (err) {
        busService.$emit('userService.returnDeleteUserError', err);
      });
    });
  }
]);
