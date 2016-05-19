/**
 * Timeline directive.
 */
angular.module('timelineApp')
  .directive('timeline', ['busService',
    function (busService) {
      'use strict';

      return {
        restrict: 'E',
        templateUrl: '/apps/timelineApp/directive/timeline.html?' + window.config.version,
        scope: {
          id: '@',
          data: '='
        },
        link: function (scope) {
          var timeline;
          var date;
          var startOfWeek = null;
          var endOfWeek = null;

          /**
           * Calculate the timeline window.
           * @param date
           */
          var calculateWindow = function calculateWindow(date) {
            // Calculate current window timestamps (current week)
            startOfWeek = date.getTime();
            startOfWeek = startOfWeek
              - (startOfWeek % (24 * 60 * 60 * 1000)                // Start of day
              - date.getTimezoneOffset() * 60 * 1000)               // Apply time zone
              - ((date.getDay() + 6) % 7) * 24 * 60 * 60 * 1000;    // Go back to monday
            endOfWeek = startOfWeek + 7 * 24 * 60 * 60 * 1000;      // Monday + 7 days

            scope.start = new Date(startOfWeek);
            scope.end = new Date(endOfWeek);
          };
          calculateWindow(date = new Date());

          // Configuration for the Timeline
          var options = {
            locale: 'da',                               // set language to danish, requires moment-with-locales.min.js
            editable: {                                 // make items editable, only update time
              add: false,
              updateTime: true,
              updateGroup: false,
              remove: false
            },
            snap: function (date) {                     // snap to hour
              var hour = 60 * 60 * 1000;
              return Math.round(date / hour) * hour;
            },
            zoomable: false,
            start: startOfWeek,                         // initial start of timeline
            end: endOfWeek,                             // initial end of timeline
            minHeight: 300                              // minimum height in pixels
          };

          // Listen for when scope is ready.
          scope.$watch('id', function (oldVal, newVal) {
            if (newVal !== null) {
              // DOM element where the Timeline will be attached
              var container = document.getElementById(scope.id);

              // Create a DataSet (allows two way data-binding)
              var items = new vis.DataSet(scope.data.channels);

              // Create a Timeline
              timeline = new vis.Timeline(container, items, scope.data.regions, options);
            }
          });

          /**
           * Move window @days number of days.
           * @param days
           */
          scope.moveDays = function moveDays(days) {
            var displacement = days * 24 * 60 * 60 * 1000;

            date = new Date(date.getTime() + displacement);

            calculateWindow(date);

            // Set new window
            timeline.setWindow({
              start: startOfWeek,
              end: endOfWeek,
              animation: false
            });

            // @TODO: Request and set data for current window.
          };
        }
      };
    }
  ]);
