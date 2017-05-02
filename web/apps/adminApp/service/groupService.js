/**
 * @file
 * Contains the group service.
 */

/**
 * Group service.
 */
angular.module('adminApp').service('groupService', ['$http', '$q',
  function ($http, $q) {
    'use strict';

    /**
     * Get groups.
     */
    this.getGroups = function () {
      var defer = $q.defer();

      $http.get('/api/group')
      .success(function (data) {
        defer.resolve(data);
      })
      .error(function () {
        defer.reject();
      });

      return defer.promise;
    };

    /**
     * Get groups.
     */
    this.createGroup = function (title) {
      var defer = $q.defer();

      $http.post('/api/group', { title: title })
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
