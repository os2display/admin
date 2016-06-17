/**
 * @file
 * Contains the screen factory.
 */

/**
 * Screen factory. Main entry point for screens and screen groups.
 */
angular.module('ikApp').factory('screenFactory', ['$http', '$q', 'busService',
  function ($http, $q, busService) {
    'use strict';

    var factory = {};
    var currentScreen = null;

    /**
     * Search via search_node.
     * @param search
     * @returns {*|Number}
     */
    factory.searchScreens = function (search) {
      var deferred = $q.defer();

      search.type = 'Indholdskanalen\\MainBundle\\Entity\\Screen';

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
     * Get all screens.
     *
     * @returns {Array}
     */
    factory.getScreens = function () {
      var defer = $q.defer();

      $http.get('/api/screen')
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Load the screens with the given ids.
     *
     * @param ids
     */
    factory.loadScreensBulk = function loadScreensBulk(ids) {
      var defer = $q.defer();

      // Build query string.
      var queryString = "?ids[]=" + (ids.join('&ids[]='));

      // Load bulk.
      $http.get('/api/bulk/screen/api' + queryString)
        .success(function (data, status) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status)
        });

      return defer.promise;
    };

    /**
     * Get the current screen.
     * @param id
     * @returns {promiseAndHandler.promise|*|Promise._progressUnchecked.promise|promise|exports.exports.Reduction.promise|PromiseResolver.promise}
     */
    factory.getEditScreen = function (id) {
      var defer = $q.defer();

      if (id === null || id === undefined || id === '') {
        defer.resolve(currentScreen);
      } else {
        $http.get('/api/screen/' + id)
          .success(function (data) {
            currentScreen = data;
            defer.resolve(currentScreen);
          })
          .error(function (data, status) {
            defer.reject(status);
          });
      }

      return defer.promise;
    };

    /**
     * Find the screen with @id
     * @param id
     * @returns screen or null
     */
    factory.getScreen = function (id) {
      var defer = $q.defer();

      $http.get('/api/screen/' + id)
        .success(function (data) {
          defer.resolve(data);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Saves screen.
     */
    factory.saveScreen = function () {
      var defer = $q.defer();

      if (currentScreen === null) {
        defer.reject(404);
      } else {
        $http.post('/api/screen', currentScreen)
          .success(function (data) {
            currentScreen.id = data;
            defer.resolve(data);
          })
          .error(function (data, status) {
            defer.reject(status);
          });
      }

      return defer.promise;
    };

    /**
     * Returns an empty screen.
     * @returns screen (empty)
     */
    factory.emptyScreen = function () {
      currentScreen = {
        id: null,
        template: null,
        description: '',
        title: '',
        orientation: 'landscape',
        width: 1920,
        height: 1080,
        channel_screen_regions: [],
        options: {}
      };

      return currentScreen;
    };

    return factory;
  }
]);

