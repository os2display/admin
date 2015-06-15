/**
 * @file
 * Contains the log Module.
 */

// Check that window.config.itkLog exists.
if (!window.config || !window.config.itkLog) {
  throw "itkLog Exception: window.config.itkLog does not exist";
}

/**
 * itkLog module.
 *
 * Consists of
 *   itkLog that is used to log messages.
 *   itk-log (itkLog) directive that is used to display log messages.
 *
 * requires stacktrace.js - http://www.stacktracejs.com/
 *   tested with v0.6.4
 */
var app = angular.module('itkLog', []);

/**
 * itkLog
 */
app.factory('itkLog', ['$http', '$timeout', '$log',
    function ($http, $timeout, $log) {
      'use strict';

      var config = window.config.itkLog;

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
        if (config.logLevel !== 'none') {
          var error = {
            "type": "error",
            "date": new Date(),
            "message": "" + message,
            "cause": cause,
            "stacktrace": printStackTrace()
          };

          factory.message = error;

          if (config.logToConsole) {
            $log.error(error);
          }

          if (config.errorCallback) {
            $http.post(config.errorCallback, error);
          }
        }
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
        if (config.logLevel === 'all') {
          factory.message = {
            "type": "log",
            "date": new Date(),
            "message": message
          };

          if (config.logToConsole) {
            $log.log(message);
          }

          if (timeout) {
            $timeout(function () {
              factory.message = null;
            }, timeout);
          }
        }
      };

      /**
       * Info message.
       *
       * @param message
       *   Info message.
       * @param timeout
       *   Clear log after timeout, if set.
       */
      factory.info = function log(message, timeout) {
        if (config.logLevel === 'all') {
          factory.message = {
            "type": "info",
            "date": new Date(),
            "message": message
          };

          if (config.logToConsole) {
            $log.info(message);
          }

          if (timeout) {
            $timeout(function () {
              factory.message = null;
            }, timeout);
          }
        }
      };

      /**
       * Warn message.
       *
       * @param message
       *   Warn message.
       * @param timeout
       *   Clear log after timeout, if set.
       */
      factory.warn = function warn(message, timeout) {
        if (config.logLevel === 'all') {
          factory.message = {
            "type": "warn",
            "date": new Date(),
            "message": message
          };

          if (config.logToConsole) {
            $log.warn(message);
          }

          if (timeout) {
            $timeout(function () {
              factory.message = null;
            }, timeout);
          }
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
 * Displays the current message from itkLog.
 */
app.directive('itkLog', ['itkLog',
    function (itkLog) {
      'use strict';

      var config = window.config.itkLog;

      return {
        restrict: 'E',
        templateUrl: 'assets/modules/itkLog/log.html?' + config.version,
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
            itkLog.clear();
          };

          /**
           * Get exception.
           */
          scope.getLogMessage = function getLogMessage() {
            return itkLog.message;
          };
        }
      };
    }
  ]);
