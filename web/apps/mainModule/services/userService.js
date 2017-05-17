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
      return currentUser;
    };

    /**
     * Get current user event listener.
     */
    busService.$on('userService.getCurrentUser', function requestUser(event, args) {
      var user = getCurrentUser();

      busService.$emit('userService.returnCurrentUser', user);
    });

    this.getCurrentUser = getCurrentUser;
  }
]);
