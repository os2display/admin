/**
 * @file
 * Contains the shared channel factory.
 */

/**
 * Shared Channel factory. Main entry point for accessing shared channels.
 */
ikApp.factory('sharedChannelFactory', ['$http', '$q', 'sharedSearchFactory',
  function($http, $q, sharedSearchFactory) {
    var factory = {};

    /**
     * Search via share Factory.
     * @param search
     * @param indexName
     * @returns {*|Number}
     */
    factory.searchChannels = function(search, indexName) {
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
        .success(function(data) {
          defer.resolve(data);
        })
        .error(function(data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    factory.getAvailableIndexes = function() {
      var defer = $q.defer();

      var available = [
        {
          name: 'ITK Dev Share',
          customer_id: 'itkdevshare'
        },
        {
          name: 'Biblioteks Share',
          customer_id: 'bibshare'
        },
        {
          name: 'Fiskedeling',
          customer_id: 'altforfiskene'
        }
      ];
      defer.resolve(available);

      return defer.promise;
    };

    factory.saveSharingIndexes = function(indexes) {
      var defer = $q.defer();

      $http.post('/api/sharing/indexes', indexes)
        .success(function(data) {
          sharingIndexes = data;
          defer.resolve(data);
        })
        .error(function(data, status) {
          defer.reject(status);
        });

      return defer.promise;
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
