/**
 * @file
 * Contains the user service.
 */

/**
 * User service.
 */
angular.module('adminApp').service('userService', ['$http', '$q',
  function ($http, $q) {
    'use strict';

    /**
     * Get current user.
     */
    this.getCurrentUser = function () {
      var defer = $q.defer();

      $http.get('/api/user/current')
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function () {
          defer.reject();
        });

      return defer.promise;
    };

    /**
     * Get users.
     */
    this.getUsers = function () {
      var defer = $q.defer();

      $http.get('/api/user')
      .success(function (data) {
        defer.resolve(data);
      })
      .error(function () {
        defer.reject();
      });

      return defer.promise;
    };
  }
]);
