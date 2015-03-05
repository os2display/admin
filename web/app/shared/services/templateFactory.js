/**
 * @file
 * Contains the template factory.
 */

/**
 * Template factory. Main entry point for templates.
 */
angular.module('ikApp').factory('templateFactory', ['$q', '$http',
  function ($q, $http) {
    'use strict';

    var factory = {};
    var slideTemplates = null;
    var screenTemplates = null;

    /**
     * Gets slide templates from cache or symfony.
     *
     * @returns {templates|*}
     */
    factory.getSlideTemplates = function () {
      var defer = $q.defer();

      if (slideTemplates !== null) {
        defer.resolve(slideTemplates);
      }
      else {
        $http.get('/api/templates/slides')
          .success(function (data) {
            slideTemplates = data;
            defer.resolve(slideTemplates);
          })
          .error(function (data, status) {
            defer.reject(status);
          });
      }

      return defer.promise;
    };

    /**
     * Get slide template with id from cache or symfony.
     *
     * @param id
     * @returns {*}
     */
    factory.getSlideTemplate = function (id) {
      var defer = $q.defer();

      if (slideTemplates !== null) {
        defer.resolve(slideTemplates[id]);
      }
      else {
        factory.getSlideTemplates().then(
          function (data) {
            defer.resolve(data[id]);
          },
          function (reason) {
            defer.reject(reason);
          }
        );
      }

      return defer.promise;
    };

    /**
     * Gets screen templates from cache or symfony.
     *
     * @returns {templates|*}
     */
    factory.getScreenTemplates = function () {
      var defer = $q.defer();

      if (screenTemplates !== null) {
        defer.resolve(screenTemplates);
      }
      else {
        $http.get('/api/templates/screens')
          .success(function (data) {
            screenTemplates = data;
            defer.resolve(screenTemplates);
          })
          .error(function (data, status) {
            defer.reject(status);
          });
      }

      return defer.promise;
    };

    /**
     * Get screen template with id from cache or symfony.
     *
     * @param id
     * @returns {*}
     */
    factory.getScreenTemplate = function (id) {
      var defer = $q.defer();

      if (screenTemplates !== null) {
        defer.resolve(screenTemplates[id]);
      }
      else {
        factory.getScreenTemplates().then(
          function (data) {
            defer.resolve(data[id]);
          },
          function (reason) {
            defer.reject(reason);
          }
        );
      }

      return defer.promise;
    };

    return factory;
  }
]);