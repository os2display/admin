/**
 * @file
 * Contains the shared channel factory.
 */

/**
 * Shared Channel factory. Main entry point for accessing shared channels.
 */
ikApp.factory('sharedChannelFactory', ['$http', '$q', 'sharedSearchFactory',
  function($http, $q, shareFactory) {
    var factory = {};

    /**
     * Search via share Factory.
     * @param search
     * @returns {*|Number}
     */
    factory.searchChannels = function(search) {
      search.type = 'Indholdskanalen\\MainBundle\\Entity\\Channel';
      return shareFactory.search(search);
    };

    /**
     * Get the available sharing indexes.
     * @returns array of sharing indexes.
     */
    factory.getSharingIndexes = function() {
      var defer = $q.defer();

      $http.get('/api/sharing/indexes')
        .success(function(data) {
          defer.resolve(data);
        })
        .error(function(data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    return factory;
  }
]);
