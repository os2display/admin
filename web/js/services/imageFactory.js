ikApp.factory('imageFactory', ['$http', '$q', function($http, $q) {
    var factory = {};
    factory.getImages = function() {
      var defer = $q.defer();

      $http.get('/media/list')
        .success(function(data) {
          defer.resolve(data);
        })
        .error(function() {
          defer.reject();
        });

      return defer.promise;
    }

    return factory;
}]);
