/**
 * @file
 * Contains the authentication http interceptor service.
 *
 * Listen to all calls through $httpProvider and intercepts and redirects to /login if
 * response is a "401 Unauthorized"
 */

/**
 * Interceptor for 401 (unauthorized) responses.
 * Logs out.
 */
angular.module('mainModule').service('authHttpResponseInterceptor', [
  '$q', '$window',
  function ($q, $window) {
    'use strict';

    this.responseError = function (rejection) {
      if (rejection.status === 401) {
        $window.location.href = "/logout";
      }
    };
  }
]);
