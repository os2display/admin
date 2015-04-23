/**
 * @file
 * Contains the log Module.
 */

/**
 * itkLog module.
 *
 * Consists of
 *   itkLogFactory that is used to log messages.
 *   itk-log (itkLog) directive that is used to display log messages.
 *
 * requires stacktrace.js - http://www.stacktracejs.com/
 *   tested with v0.6.4
 */
var app = angular.module('itkLog', []);
app.factory('itkLogFactory', ['$http', '$timeout', '$log',
    function ($http, $timeout, $log) {
      'use strict';

      var factory = {};

      factory.message = null;

      /**
       * Log an error.
       * And post to backend.
       *
       * @param message
       *   Error message.
       * @param cause
       *   Cause of error.
       */
      factory.error = function error(message, cause) {
        var error = {
          "type": "error",
          "date": new Date(),
          "message": message,
          "cause": cause,
          "stacktrace": printStackTrace()
        };

        factory.message = error;

        $log.error(error);

        $http.post('api/error', error);
      };

      /**
       * Log a message.
       *
       * @param message
       *   Message to log.
       * @param timeout
       *   Clear log after timeout, if set.
       */
      factory.log = function log(message, timeout) {
        factory.message = {
          "type": "log",
          "date": new Date(),
          "message": message
        };

        $log.log(message);

        if (timeout) {
          $timeout(function() {
            factory.message = null;
          }, timeout);
        }
      };

      /**
       * Clear latest exception.
       */
      factory.clear = function () {
        factory.message = null;
      };

      return factory;
    }
  ]
);

/**
 * itk-log directive.
 *
 * Displays the current message from itkLogFactory.
 */
app.directive('itkLog', ['itkLogFactory', function (itkLogFactory) {
    'use strict';

    return {
      restrict: 'E',
      templateUrl: 'assets/modules/itkLog/log.html',
      link: function (scope) {
        scope.expanded = false;

        /**
         * Expand/Collapse extra info.
         */
        scope.toggleExpanded = function toggleExpanded() {
          scope.expanded = !scope.expanded;
        };

        /**
         * Clear log.
         */
        scope.clearLog = function clearLog() {
          itkLogFactory.clear();
        };

        /**
         * Get exception.
         */
        scope.getLogMessage = function getLogMessage() {
          return itkLogFactory.message;
        };
      }
    };
  }]
);
