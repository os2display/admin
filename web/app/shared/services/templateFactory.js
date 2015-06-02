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
        $http.get('/api/templates/slides/enabled')
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

      factory.getSlideTemplates().then(
        function (data) {
          for (var i = 0; i < data.length; i++) {
            if (data[i].id === id) {
              defer.resolve(data[i]);
              return;
            }
          }
          defer.reject(404);
        },
        function (reason) {
          defer.reject(reason);
        }
      );

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
        $http.get('/api/templates/screens/enabled')
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
            for (var i = 0; i < data.length; i++) {
              if (data[i].id === id) {
                defer.resolve(data[i]);
                return;
              }
            }
            defer.reject(404);
          },
          function (reason) {
            defer.reject(reason);
          }
        );
      }

      return defer.promise;
    };

    /**
     * Save which templates are enabled.
     *
     * @param enabledScreenTemplates
     * @param enabledSlideTemplates
     */
    factory.saveEnabledTemplates = function (enabledScreenTemplates, enabledSlideTemplates) {
      var defer = $q.defer();

      $http.post('/api/templates/save/enabled', {
        "screens": enabledScreenTemplates,
        "slides": enabledSlideTemplates
      })
        .success(function (data, status) {
          $http.get('/api/templates/slides/enabled')
            .success(function (data) {
              slideTemplates = data;
            })
            .error(function (data, status) {
              // @TODO: Handle this.
            });
          $http.get('/api/templates/screen/enabled')
            .success(function (data) {
              screenTemplates = data;
            })
            .error(function (data, status) {
              // @TODO: Handle this.
            });

          defer.resolve(status);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Get all slide templates.
     * @returns {*}
     */
    factory.getAllSlideTemplates = function () {
      var defer = $q.defer();

      $http.get('/api/templates/slides/all')
        .success(function (data) {
          slideTemplates = data;
          defer.resolve(slideTemplates);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    /**
     * Get all screen templates.
     * @returns {*}
     */
    factory.getAllScreenTemplates = function () {
      var defer = $q.defer();

      $http.get('/api/templates/screens/all')
        .success(function (data) {
          slideTemplates = data;
          defer.resolve(slideTemplates);
        })
        .error(function (data, status) {
          defer.reject(status);
        });

      return defer.promise;
    };

    return factory;
  }
]);