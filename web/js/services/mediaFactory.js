/**
 * @file
 * Contains the media factory.
 */

/**
 * Media factory. Main entry to media.
 */
ikApp.factory('mediaFactory', ['$http', '$q', 'searchFactory',
  function($http, $q, searchFactory) {
    var factory = {};

    /**
     * Get all media.
     */
    factory.getAllMedia = function() {
      var defer = $q.defer();

      $http.get('/api/media')
        .success(function(data) {
          defer.resolve(data);
        })
        .error(function() {
          defer.reject();
        });

      return defer.promise;
    };

    /**
     * Search for media defined by search parameter.
     * @param search
     */
    factory.searchMedia = function(search) {
      search.type = 'Application\\Sonata\\MediaBundle\\Entity\\Media';
      return searchFactory.search(search);
    };

    /**
     * Find the media with @id
     * @param id
     */
    factory.getMedia = function(id) {
      var defer = $q.defer();

      $http.get('/api/media/' + id)
        .success(function(data) {
          defer.resolve(data);
        })
        .error(function(data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Delete the media with @id
     * @param id
     */

    factory.deleteMedia = function(id) {
      var defer = $q.defer();

      $http.delete('/api/media/' + id)
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
