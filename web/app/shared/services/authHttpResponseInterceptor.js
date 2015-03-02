/**
 * @file
 * Contains the authentication http interceptor factory.
 *
 * Listen to all calls through $httpProvider and intercepts and redirects to /login if
 * response is a "401 Unauthorized"
 */

/**
 * Interceptor for 401 (unauthorized) responses.
 * Logs out.
 */
angular.module('ikApp').factory('authHttpResponseInterceptor', ['$q', '$location', '$window',
  function ($q, $location, $window) {
    return {
      responseError: function (rejection) {
        if (rejection.status === 401) {
          $window.location.href = "/login";
        }
        return $q.reject(rejection);
      }
    }
  }
]);

/**
 * Register the event interceptor.
 */
angular.module('ikApp').config(['$httpProvider', function ($httpProvider) {
  $httpProvider.interceptors.push('authHttpResponseInterceptor');
}]);