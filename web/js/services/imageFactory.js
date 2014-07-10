ikApp.factory('imageFactory', ['$http', '$q', 'searchFactory', function($http, $q, searchFactory) {
  var factory = {};

  factory.searchImages = function(search) {
    search.type = 'Application\\Sonata\\MediaBundle\\Entity\\Media';
    search.app_id = 1234;

    return searchFactory.search(search);
  };

  return factory;
}]);
