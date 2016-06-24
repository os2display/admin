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
           *   The event the happened.
           * @param args
           *   The message object.
           */
          busService.$on('messages.add', function message(event, message) {
            scope.$apply(function() {
              scope.messages.push(message);

              // Automatically remove message if timeout defined.
              if (message.timeout !== undefined) {
                $timeout(function() {
                  scope.close(message.$$hashKey);
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
           * @param hashKey
           *   The hashkey of the message to remove.
           */
          scope.close = function close(hashKey) {
            for (var i = 0; i < scope.messages.length; i++) {
              if (scope.messages[i].$$hashKey === hashKey) {
                scope.messages.splice(i, 1);
              }
            }
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