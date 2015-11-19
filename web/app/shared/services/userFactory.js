/**
 * @file
 * Contains the user factory.
 */

/**
 * User factory.
 */
angular.module('ikApp').factory('userFactory', ['$http', '$q',
  function ($http, $q) {
    'use strict';

    var factory = {};
    var cache;

    /**
     * Get current user.
     */
    factory.getCurrentUser = function () {
      var defer = $q.defer();

      if (cache === undefined) {
        $http.get('/api/user')
          .success(function (data) {
            cache = data;
            defer.resolve(data);
          })
          .error(function () {
            defer.reject();
          });
      }
      else {
        defer.resolve(cache);
      }

      return defer.promise;
    };

    return factory;
  }
]);
