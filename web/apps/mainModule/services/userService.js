/**
 * User service.
 */
angular.module('mainModule').service('userService', [
  'busService',
  function (busService) {
    'use strict';

    // Get
    var currentUser = OS2DISPLAY_CURRENT_USER;

    /**
     * Get the current user.
     *
     * @return object
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

    // Expose methods.
    this.getCurrentUser = getCurrentUser;
  }
]);
