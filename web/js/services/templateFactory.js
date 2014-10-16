/**
 * @file
 * Contains the template factory.
 */

/**
 * Template factory. Main entry point for templates.
 */
ikApp.factory('templateFactory', ['$q', '$http',
  function($q, $http) {
    var factory = {};
    var templates = null;
    var orderedTemplates = null;

    /**
     * Gets templates from cache or symfony.
     *
     * @returns {templates|*}
     */
    factory.getTemplates = function() {
      var defer = $q.defer();

      if (templates !== null) {
        defer.resolve(templates);
      }
      else {
        $http.get('/api/templates/')
          .success(function(data, status) {
            templates = data;
            orderedTemplates = [];
            templates.forEach(function(element, index) {
              orderedTemplates[element.id] = element;
            });
            defer.resolve(templates);
          })
          .error(function(data, status) {
            defer.reject(status);
          });
      }

      return defer.promise;
    };

    /**
     * Get template with id from cache or symfony.
     *
     * @param id
     * @returns {*}
     */
    factory.getTemplate = function(id) {
      var defer = $q.defer();

      if (orderedTemplates !== null) {
        defer.resolve(orderedTemplates[id]);
      }
      else {
        factory.getTemplates().then(
          function(data) {
            defer.resolve(orderedTemplates[id]);
          },
          function(reason) {
            defer.reject(reason);
          }
        );
      }

      return defer.promise;
    };


    return factory;
  }
]);