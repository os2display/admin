/**
 * @file
 * Contains the media factory.
 */

/**
 * Media factory. Main entry to media.
 */
angular.module('ikApp').factory('mediaFactory', ['$http', '$q', 'searchFactory',
  function ($http, $q, searchFactory) {
    'use strict';

    var factory = {};

    /**
     * Get all media.
     */
    factory.getAllMedia = function () {
      var defer = $q.defer();

      $http.get('/api/media')
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function () {
          defer.reject();
        });

      return defer.promise;
    };

    /**
     * Search for media defined by search parameter.
     * @param search
     */
    factory.searchMedia = function (search) {
      search.type = 'Application\\Sonata\\MediaBundle\\Entity\\Media';
      return searchFactory.search(search);
    };

    /**
     * Load the screens with the given ids.
     *
     * @param ids
     */
    factory.loadMediaBulk = function loadMediaBulk(ids) {
      var defer = $q.defer();

      // Build query string.
      var queryString = "?";
      for (var i = 0; i < ids.length; i++) {
        queryString = queryString + "ids[]=" + ids[i];
        if (i < ids.length - 1) {
          queryString = queryString + "&"
        }
      }

      // Load bulk.
      $http.get('/api/bulk/media/api' + queryString)
        .success(function (data, status) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status)
        });

      return defer.promise;
    };

    /**
     * Find the media with @id
     * @param id
     */
    factory.getMedia = function (id) {
      var defer = $q.defer();

      $http.get('/api/media/' + id)
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Delete the media with @id
     * @param id
     */

    factory.deleteMedia = function (id) {
      var defer = $q.defer();

      $http.delete('/api/media/' + id)
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    return factory;
  }
]);
