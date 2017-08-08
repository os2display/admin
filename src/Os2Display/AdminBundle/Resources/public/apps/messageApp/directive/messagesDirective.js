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
        templateUrl: 'bundles/os2displayadmin/apps/messageApp/directive/messages.html?' + config.version,
        link: function (scope) {
          scope.expanded = false;
          scope.messages = {};

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
              // Build hash value to identify a given message.
              var hash = CryptoJS.MD5(JSON.stringify(message)).toString();
              message.hash = hash;

              // Add message to scope.
              scope.messages[hash] = message;

              // Automatically remove message if timeout defined.
              if (message.timeout !== undefined) {
                $timeout(function() {
                  scope.close(message);
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
           * @param message
           *   The message to remove.
           */
          scope.close = function close(message) {
            delete scope.messages[message.hash];
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