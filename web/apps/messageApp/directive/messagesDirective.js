/**
 * Log directive.
 *
 * Displays the current message from itkLog.
 */
angular.module('messageApp')
  .directive('messages', ['busService',
    function (busService) {
      'use strict';

      var config = window.config.itkLog;

      return {
        restrict: 'E',
        templateUrl: '/apps/messageApp/directive/messages.html?' + config.version,
        link: function (scope) {
          scope.expanded = false;
          scope.messages = [];

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
            scope.$apply(function() {
              scope.messages.push(message);
            });
          });

          /**
           * Clear displayed message.
           */
          busService.$on('log.clear', function clear(event, args) {
            scope.clear();
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
          scope.clear = function clear() {
            scope.messages = [];
          };
        }
      };
    }
  ]);