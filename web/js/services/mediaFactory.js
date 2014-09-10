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
     * Get all images.
     */
    factory.getImages = function() {
      var defer = $q.defer();

      $http.get('/api/media')
        .success(function(data) {
          defer.resolve(data);
        })
        .error(function() {
          defer.reject();
        });

      return defer.promise;
    }

    /**
     * Search for images defined by search parameter.
     * @param search
     */
    factory.searchMedia = function(search) {
      search.type = 'Application\\Sonata\\MediaBundle\\Entity\\Media';
      return searchFactory.search(search);
    };

    /**
     * Find the image with @id
     * @param id
     */
    factory.getImage = function(id) {
      var defer = $q.defer();

      $http.get('/api/media/' + id)
        .success(function(data, status) {
          defer.resolve(data);
        })
        .error(function(data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Delete the image with @id
     * @param id
     */

    factory.deleteImage = function(id) {
      var defer = $q.defer();

      $http.delete('/api/media/' + id)
        .success(function(data, status) {
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
