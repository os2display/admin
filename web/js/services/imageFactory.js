/*
@TODO Re-implement getting images from ES

ikApp.factory('imageFactory', ['$http', '$q', 'searchFactory', function($http, $q, searchFactory) {
  var factory = {};

  factory.searchImages = function(search) {
    search.type = 'Application\\Sonata\\MediaBundle\\Entity\\Media';
    search.app_id = 1234;

    return searchFactory.search(search);
  };

  return factory;
}]);

*/


ikApp.factory('imageFactory', ['$http', '$q', function($http, $q) {
    var factory = {};
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

    factory.searchImages = function(search) {
        return this.getImages();
    }


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

    return factory;

}]);