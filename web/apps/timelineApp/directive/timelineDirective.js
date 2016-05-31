/**
 * Timeline directive.
 */
angular.module('timelineApp')
  .directive('timeline', ['busService', '$timeout',
    function (busService, $timeout) {
      'use strict';

      return {
        restrict: 'E',
        templateUrl: '/apps/timelineApp/directive/timeline.html?' + window.config.version,
        scope: {
          tid: '@',
          data: '='
        },
        link: function (scope) {
          var timeline;
          var date;
          var items;

          scope.start = null;
          scope.end = null;

          /**
           * Calculate the timeline window.
           * @param date
           */
          var calculateWeekWindow = function calculateWeekWindow(date) {
            // Calculate current window timestamps (current week)
            var startOfWeek = date.getTime();
            startOfWeek = startOfWeek
              - (startOfWeek % (24 * 60 * 60 * 1000)                    // Start of day
              - date.getTimezoneOffset() * 60 * 1000)                   // Apply time zone
              - ((date.getDay() + 6) % 7) * 24 * 60 * 60 * 1000;        // Go back to monday
            var endOfWeek = startOfWeek + 7 * 24 * 60 * 60 * 1000;      // Monday + 7 days

            timeline.setWindow({
              start: startOfWeek,
              end: endOfWeek,
              animation: false
            });
          };

          /**
           * Calculate calendar data for the current range between scope.start and scope.end
           */
          var calculateData = function calculateData() {
            items = [];

            for (var i = 0; i < scope.data.channels.length; i++) {
              var channel = scope.data.channels[i];
              var item;

              if ((!channel.start || channel.start < scope.end.getTime()) &&
                  (!channel.end   || channel.end > scope.start.getTime())) {
                if (!channel.schedule_repeat) {
                  item = angular.copy(channel);
                  if (!item.start) {
                    item.start = scope.start.getTime();
                  }

                  if (!item.end) {
                    item.end = scope.end.getTime();
                  }

                  items.push(item);
                }
                else {
                  if (channel.schedule_repeat_days) {
                    var weekStart = new Date(scope.start);

                    for (var j = 0; j < channel.schedule_repeat_days.length; j++) {
                      var scheduleDay = (channel.schedule_repeat_days[j].id + 6 - ((weekStart.getDay() + 6) % 7)) % 7;

                      var currentDay = new Date(weekStart);
                      currentDay.setDate(currentDay.getDate() + scheduleDay);

                      item = angular.copy(channel);
                      item.start = new Date(currentDay);
                      item.start.setHours(channel.schedule_repeat_from ? channel.schedule_repeat_from : 0);
                      item.start = item.start.getTime();
                      item.end = new Date(currentDay);
                      item.end.setHours(channel.schedule_repeat_to ? channel.schedule_repeat_to : 23);
                      item.end.setMinutes(channel.schedule_repeat_to ? 0 : 59);
                      item.end.setSeconds(channel.schedule_repeat_to ? 0 : 59);
                      item.end = item.end.getTime();

                      // @TODO: Handle item.end item.start overflowing channel.start and channel.end

                      item.id = item.id + "_" + j;

                      items.push(item);
                    }
                  }
                }
              }
            }

            timeline.setItems(items);
          };

          // Configuration for the Timeline
          // Configuration for the Timeline
          var options = {
            locale: 'da',                               // set language to danish, requires moment-with-locales.min.js
            editable: false,
            snap: function (date) {                     // snap to hour
              var hour = 60 * 60 * 1000;
              return Math.round(date / hour) * hour;
            },
            stack: false,
            zoomable: false                             // remove zoomable
          };

          // Listen for when scope is ready.
          scope.$watch('data', function (oldVal, newVal) {
            if (newVal !== null) {
              // DOM element where the Timeline will be attached
              var container = document.getElementById("timeline_" + scope.data.id);

              // Create a DataSet (allows two way data-binding)
              //var items = new vis.DataSet(scope.data.channels);
              var emptyItems = new vis.DataSet();

              // Create a Timeline
              timeline = new vis.Timeline(container, emptyItems, scope.data.regions, options);

              // Register listeners.
              timeline.on('rangechanged', function (properties) {
                // Update window and recalculate data.
                //   Timeout to avoid digest errors, since timeline events are outside angular.
                $timeout(function() {
                  scope.start = new Date(properties.start);
                  scope.end = new Date(properties.end);

                  calculateData();
                });
              });

              timeline.on('select', function (properties) {
                // Find item.
                for (var item in items) {
                  item = items[item];
                  if (item.id == properties.items[0]) {
                    console.log("Channel: " + item.channel_id);
                  }
                }
              });

              // Initialize window.
              //   Triggers rangechanged listener.
              date = new Date();
              calculateWeekWindow(date);
            }
          });

          /**
           * Move window @days number of days.
           * @param days
           */
          scope.moveDays = function moveDays(days) {
            var displacement = days * 24 * 60 * 60 * 1000;

            date = new Date(date.getTime() + displacement);

            calculateWeekWindow(date);
          };

          scope.today = function today() {
            date = new Date();

            calculateWeekWindow(date);
          };

          // Register event listener for destroy.
          scope.$on('$destroy', function() {
            if (timeline) {
              timeline.off();
            }
          });
        }
      };
    }
  ]);
