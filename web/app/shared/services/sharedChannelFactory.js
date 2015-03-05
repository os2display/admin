/**
 * @file
 * Contains the shared channel factory.
 */

/**
 * Shared Channel factory. Main entry point for accessing shared channels.
 */
angular.module('ikApp').factory('sharedChannelFactory', ['$http', '$q', 'sharedSearchFactory',
  function ($http, $q, sharedSearchFactory) {
    'use strict';

    var factory = {};

    var availableIndexes = null;

    /**
     * Search via share Factory.
     * @param search
     * @param indexName
     * @returns {*|Number}
     */
    factory.searchChannels = function (search, indexName) {
      search.type = 'Indholdskanalen\\MainBundle\\Entity\\Channel';
      return sharedSearchFactory.search(search, indexName);
    };

    /**
     * Get a shared channel.
     * @param id
     * @param index
     */
    factory.getSharedChannel = function getSharedChannel(id, index) {
      var defer = $q.defer();

      $http.get('/api/sharing/channel/' + id + '/' + index)
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Save shared channel.
     * @param channel
     * @returns {*}
     */
    factory.saveSharedChannel = function saveSharedChannel(channel) {
      var defer = $q.defer();

      $http.post('/api/sharing/channel', channel)
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Get available indexes from sharing service
     * @returns {*}
     */
    factory.getAvailableIndexes = function () {
      var defer = $q.defer();

      if (availableIndexes !== null) {
        defer.resolve(availableIndexes);
      }
      else {
        $http.get('/api/sharing/available_indexes')
          .success(function (data) {
            availableIndexes = data;
            defer.resolve(data);
          })
          .error(function (data, status) {
            defer.reject(status);
          });
      }

      return defer.promise;
    };

    /**
     * Save sharing indexes.
     * @param indexes
     * @returns {*}
     */
    factory.saveSharingIndexes = function (indexes) {
      var defer = $q.defer();

      $http.post('/api/sharing/indexes', indexes)
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Get the available sharing indexes.
     * @returns array of sharing indexes.
     */
    factory.getSharingIndexes = function () {
      var defer = $q.defer();

      $http.get('/api/sharing/indexes')
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
