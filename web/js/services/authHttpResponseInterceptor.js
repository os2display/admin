/**
 * @file
 * Contains the authentication http interceptor factory.
 *
 * Listen to all calls through $httpProvider and intercepts and redirects to /login if
 * response is a "401 Unauthorized"
 */

ikApp.factory('authHttpResponseInterceptor',['$q','$location', '$window',function($q,$location, $window){
  return {
    responseError: function(rejection) {
      if (rejection.status === 401) {
        $window.location.href="/login";
      }
      return $q.reject(rejection);
    }
  }
}]);

ikApp.config(['$httpProvider',function($httpProvider) {
  $httpProvider.interceptors.push('authHttpResponseInterceptor');
}]);