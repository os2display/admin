/**
 * @file
 * Contains the user factory.
 */

/**
 * User factory.
 */
ikApp.factory('userFactory', ['$http', '$q',
  function($http, $q) {
    var factory = {};

    /**
     * Get current user.
     */
    factory.getCurrentUser = function() {
      var defer = $q.defer();

      $http.get('/api/user')
        .success(function(data) {
          defer.resolve(data);
        })
        .error(function() {
          defer.reject();
        });

      return defer.promise;
    };

    return factory;
  }
]);
