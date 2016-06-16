/**
 * Log directive.
 *
 * Displays the current message from itkLog.
 */
angular.module('messageApp')
  .directive('messages', ['busService', '$timeout',
    function (busService, $timeout) {
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
          busService.$on('messages.add', function message(event, message) {
            scope.$apply(function() {
              scope.messages.push(message);
              
              // Automatically remove message if timeout defined.
              if (message.timeout !== undefined) {
                var index = scope.messages.length - 1;
                $timeout(function() {
                  scope.close(index);
                }, message.timeout);
              }
            });
          });

          /**
           * Clear displayed message.
           */
          busService.$on('messages.clear', function clear(event, args) {
            scope.clear();
          });

          /**
           * Expand/Collapse extra info.
           */
          scope.toggleExpanded = function toggleExpanded() {
            scope.expanded = !scope.expanded;
          };

          /**
           * Remove/close single message.
           *
           * @param index
           *   The index of the message to remove from the messages array.
           */
          scope.close = function close(index) {
            scope.messages.splice(index, 1);
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