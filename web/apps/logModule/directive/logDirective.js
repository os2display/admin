/**
 * Log directive.
 *
 * Displays the current message from itkLog.
 */
angular.module('logModule')
  .directive('log', ['busService',
    function (busService) {
      'use strict';

      var config = window.config.itkLog;

      return {
        restrict: 'E',
        templateUrl: '/apps/logModule/directive/log.html?' + config.version,
        link: function (scope) {
          scope.expanded = false;
          scope.messages = [];

          console.log('test');

          /**
           * Listen for messages.
           *
           * @see logService
           *
           * @param event
           *   The event the happend.
           * @param args
           *   The message object send.
           */
          busService.$on('log.message', function message(event, message) {
            scope.messages.push(message);
          });

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
            scope.messages = [];
          };
        }
      };
    }
  ]);