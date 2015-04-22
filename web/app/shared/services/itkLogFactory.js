/**
 * @file
 * Contains the logging module.
 */

/**
 * Initialize the logging module.
 */
angular.module('itkLog').factory('itkLogFactory', ['$http',
  function ($http) {
    'use strict';

    var factory = {};

    factory.exception = null;

    /**
     * Log an error.
     * Display in error area.
     *
     * @param exception
     * @param cause
     */
    factory.error = function(exception, cause) {
      var error = {
        "type": "javascript error",
        "message": exception.message,
        "cause": cause,
        "stacktrace": printStackTrace()
      };

      factory.exception = error;

      $http.post('api/error', error);
    };

    /**
     * Clear latest exception.
     */
    factory.clear = function() {
      factory.exception = null;
    };

    return factory;
  }
]);
