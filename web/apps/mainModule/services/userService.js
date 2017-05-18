/**
 * User service.
 */
angular.module('mainModule').service('userService', [
  'busService',
  function (busService) {
    'use strict';

    // Get
    var currentUser = angular.copy(OS2DISPLAY_CURRENT_USER);

    /**
     * Get current user event listener.
     */
    busService.$on('userService.getCurrentUser', function requestUser(event, args) {
      busService.$emit('userService.returnCurrentUser', currentUser);
    });

    /**
     * Update current user event listener.
     */
    busService.$on('userService.apiServiceReturnCurrentUser', function returnCurrentUser(event, user) {
      currentUser = user;
    });

    /**
     * Update the current user.
     */
    this.updateCurrentUser = function updateCurrentUser() {
      busService.$emit('apiService.request', {
        'method': 'get',
        'url': 'api/user/current',
        'returnEvent': 'userService.apiServiceReturnCurrentUser'
      });
    };

    /**
     * Get current user.
     */
    this.getCurrentUser = function getCurrentUser() {
      return currentUser;
    };

    /**
     * Check if user (default: current user) has a specified role.
     */
    this.hasRole = function hasRole(role, user) {
      user || (user = currentUser);

      return user && user.api_data && user.api_data.roles &&
        (Object.keys(user.api_data.roles).map(function(key){return user.api_data.roles[key]})).indexOf(role) !== -1;
    };

    /**
     * Get current users groups.
     */
    this.getCurrentUserGroups = function getCurrentUserGroups(returnEvent) {
      busService.$emit('apiService.getEntities', {
        'type': 'group',
        'returnEvent': returnEvent
      });
    }
  }
]);
