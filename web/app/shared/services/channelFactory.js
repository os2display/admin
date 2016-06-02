/**
 * @file
 * Contains the channel factory.
 */

/**
 * Channel factory. Main entry point for accessing channels.
 */
angular.module('ikApp').factory('channelFactory', ['$http', '$q', 'searchFactory',
  function ($http, $q, searchFactory) {
    'use strict';

    var factory = {};

    // Current open channel.
    // This is the channel we are editing.
    var currentChannel = {};

    /**
     * Search via search_node.
     * @param search
     * @returns {*|Number}
     */
    factory.searchChannels = function (search) {
      search.type = 'Indholdskanalen\\MainBundle\\Entity\\Channel';
      return searchFactory.search(search);
    };

    /**
     * Get all channels.
     */
    factory.getChannels = function () {
      var defer = $q.defer();

      $http.get('/api/channel')
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function () {
          defer.reject();
        });

      return defer.promise;
    };

    /**
     * Load the channels with the given ids.
     *
     * @param ids
     */
    factory.loadChannelsBulk = function loadChannelsBulk(ids) {
      var defer = $q.defer();

      // Build query string.
      var queryString = "?";
      for (var i = 0; i < ids.length; i++) {
        queryString = queryString + "ids[]=" + ids[i];
        if (i < ids.length - 1) {
          queryString = queryString + "&";
        }
      }

      // Load bulk.
      $http.get('/api/bulk/channel/api-bulk' + queryString)
        .success(function (data, status) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Find slide to edit. If id is not set return current slide, else load from backend.
     * @param id
     */
    factory.getEditChannel = function (id) {
      var defer = $q.defer();

      if (id === null || id === undefined || id === '') {
        defer.resolve(currentChannel);
      } else {
        $http.get('/api/channel/' + id)
          .success(function (data) {
            currentChannel = data;
            defer.resolve(currentChannel);
          })
          .error(function () {
            defer.reject();
          });
      }

      return defer.promise;
    };

    /**
     * Find the channel with @id
     * @param id
     */
    factory.getChannel = function (id) {
      var defer = $q.defer();

      $http.get('/api/channel/' + id)
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function () {
          defer.reject();
        });

      return defer.promise;
    };

    /**
     * Returns an empty channel.
     */
    factory.emptyChannel = function () {
      currentChannel = {
        id: null,
        title: '',
        orientation: '',
        created_at: parseInt((new Date().getTime()) / 1000),
        slides: [],
        screens: [],
        sharing_indexes: [],
        schedule_repeat_days: []
      };

      return currentChannel;
    };

    /**
     * Saves channel to channels. Assigns an id, if it is not set.
     */
    factory.saveChannel = function () {
      var defer = $q.defer();

      $http.post('/api/channel', currentChannel)
        .success(function () {
          defer.resolve("success");
          currentChannel = null;
        })
        .error(function () {
          defer.reject("error");
        });

      return defer.promise;
    };

    /**
     * Update which indexes a channel is shared with.
     * @param channel
     * @returns {*}
     */
    factory.channelShare = function (channel) {
      var defer = $q.defer();

      $http.post('/api/channel/share', channel)
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
