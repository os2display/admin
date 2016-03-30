/**
 * @file
 * Contains the log Module.
 */

// Check that window.config.itkLog exists.
// @TODO: Rename itkLog to log in config.
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
angular.module('logModule')
  .service('logService', ['busService', '$http', '$timeout', '$log',
    function (busService, $http, $timeout, $log) {
      'use strict';

      var config = window.config.itkLog;

      /**
       * Build generic log message object.
       *
       * @param type
       *   The type of log event (error, log, info, etc.)
       * @param timeout
       *   The timeout to show the message.
       * @param cause
       *   What caused the message to happen.
       * @param msg
       *   The log message
       *
       * @returns {{type: *, date: Date, message: *, cause: *, stacktrace: *}}
       */
      function buildMessage(type, timeout, cause, msg) {
        return {
          "type": type,
          "date": new Date(),
          "message": msg,
          "cause": cause,
          "stacktrace": printStackTrace()
        };
      }

      /**
       * Log an error.
       *
       * It can be configured to be send to console.error or/and the backend.
       *
       * @param event
       *   The event send over the bus.
       * @param args
       *   JSON args that contains the cause and message.
       */
      busService.$on('log.error', function error(event, args) {
        if (config.logLevel !== 'none') {
          var error = buildMessage('error', args.timeout, args.cause, args.msg);

          // Send generic log message into the bus.
          busService.$emit('log.message', error);

          if (config.logToConsole) {
            $log.error(error);
          }

          if (config.errorCallback) {
            $http.post(config.errorCallback, error);
          }
        }
      });

      /**
       * Log a message.
       *
       * It can be configured to be send to console.log.
       *
       * @param event
       *   The event send over the bus.
       * @param args
       *   JSON args that contains the cause and message.
       */
      busService.$on('log.log', function log(event, args) {
        if (config.logLevel === 'all') {
          var message = buildMessage('log', args.timeout, args.cause, args.msg);

          // Send generic log message into the bus.
          busService.$emit('log.message', message);

          if (config.logToConsole) {
            $log.log(message);
          }
        }
      });

      /**
       * Info message.
       *
       * It can be configured to be send to console.info.
       *
       * @param event
       *   The event send over the bus.
       * @param args
       *   JSON args that contains the cause and message.
       */
      busService.$on('log.info', function info(event, args) {
        if (config.logLevel === 'all') {
          var info = buildMessage('info', args.timeout, args.cause, args.msg);

          // Send generic log message into the bus.
          busService.$emit('log.message', info);

          if (config.logToConsole) {
            $log.info(info);
          }
        }
      });

      /**
       * Warn message.
       *
       * It can be configured to be send to console.error or/and the backend.
       *
       * @param event
       *   The event send over the bus.
       * @param args
       *   JSON args that contains the cause and message.
       */
      busService.$on('log.warn', function warn(event, args) {
        if (config.logLevel === 'all') {
          var warn = buildMessage('warn', args.timeout, args.cause, args.msg);

          // Send generic log message into the bus.
          busService.$emit('log.message', warn);

          if (config.logToConsole) {
            $log.warn(warn);
          }
        }
      });
    }
  ]
);
