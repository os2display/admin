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
    var sharingIndexes = null;

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
     */
    factory.getSharedChannel = function getSharedChannel(id) {
      var defer = $q.defer();

      $http.get('/api/sharing/channel/' + id)
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
          customer_id: 'itkdevshare',
          id: 1
        },
        {
          name: 'Biblioteks Share',
          customer_id: 'bibshare',
          id: 2
        },
        {
          name: 'Fiskedeling',
          customer_id: 'altforfiskene',
          id: 3
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

      if (sharingIndexes !== null) {
        defer.resolve(sharingIndexes);
      }
      else {
        $http.get('/api/sharing/indexes')
          .success(function(data) {
            sharingIndexes = data;
            defer.resolve(data);
          })
          .error(function(data, status) {
            defer.reject(status);
          });
      }

      return defer.promise;
    };

    return factory;
  }
]);
