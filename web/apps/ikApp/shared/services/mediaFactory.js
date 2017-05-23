/**
 * @file
 * Contains the media factory.
 */

/**
 * Media factory. Main entry to media.
 */
angular.module('ikApp').factory('mediaFactory', ['$http', '$q', 'busService',
  function ($http, $q, busService) {
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
     *
     * @param search
     */
    factory.searchMedia = function (search) {
      var deferred = $q.defer();

      search.type = 'Application\\Sonata\\MediaBundle\\Entity\\Media';

      var uuid = CryptoJS.MD5(JSON.stringify(search)).toString();
      search.callbacks = {
        'hits': 'searchService.hits-' + uuid,
        'error': 'searchService.error-' + uuid
      };

      busService.$once(search.callbacks.hits, function(event, data) {
        deferred.resolve(data);
      });

      busService.$once(search.callbacks.error, function(event, args) {
        busService.$emit('log.error', {
          'cause': args,
          'msg': 'Kunne ikke hente søgeresultater.'
        });
        deferred.reject(args);
      });

      busService.$emit('searchService.request', search);

      return deferred.promise;
    };

    /**
     * Load the screens with the given ids.
     *
     * @param ids
     */
    factory.loadMediaBulk = function loadMediaBulk(ids) {
      var defer = $q.defer();

      // Build query string.
      var queryString = "?ids[]=" + (ids.join('&ids[]='));

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
