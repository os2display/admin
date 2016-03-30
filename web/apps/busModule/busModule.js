/**
 * @file
 * Service bus to handle communication between the different applications.
 *
 * All event sent into the bus must be prefixed with the app name of the app
 * sending the event (eg. searchApp.search) and if the return event needs an
 * "id" or/and "sequence" number to identify the async answer on the bus.
 */

angular.module('busModule')
  .service('busService', function($rootScope, $window){
    'use strict';

    // Store rootScopes for each service user.
    $window.rootScopes = $window.rootScopes || [];
    $window.rootScopes.push($rootScope);

    /**
     * Wrapper for emitting events to all root scopes.
     *
     * @param name
     *   Event name
     * @param args
     *   Event arguments.
     */
    this.$emit = function emit(name, args) {
       angular.forEach($window.rootScopes, function(scope) {
        scope.$emit(name, args);
      });
    };

    /**
     * Wrapper to attach event listeners to the current root scope.
     *
     * @param name
     *   Event name.
     * @param listener
     *   The listener to call on event.
     */
    this.$on = function on(name, listener) {
      $rootScope.$on(name, function (event, message) {
        listener.apply($rootScope, [event, message]);
      });
    };
  });