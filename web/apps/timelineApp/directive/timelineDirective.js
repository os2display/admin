/**
 * Timeline directive.
 */
angular.module('timelineApp')
  .directive('timeline', ['busService', '$timeout', '$location',
    function (busService, $timeout, $location) {
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
            // @TODO: is this always correct?

            // @REVIEW:
            // @See http://locutus.io/php/datetime/mktime/ or http://locutus.io/php/datetime/strtotime/
            // Inlucde web/assets/libs/locutus.io.js to use functions strtotime and mktime.

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

            for (var i = 0; i < scope.data.items.length; i++) {
              var item = angular.copy(scope.data.items[i]);

              if ((!item.start || item.start < scope.end.getTime()) &&
                  (!item.end   || item.end > scope.start.getTime())) {
                if (!item.schedule_repeat) {
                  if (!item.start) {
                    item.start = scope.start.getTime();
                  }

                  if (!item.end) {
                    item.end = scope.end.getTime();
                  }

                  items.push(item);
                }
                else {
                  if (item.schedule_repeat_days) {
                    var currentDay = new Date(scope.start);

                    var j = 0;

                    while (currentDay < scope.end) {
                      for (var k = 0; k < item.schedule_repeat_days.length; k++) {
                        if (currentDay.getDay() === item.schedule_repeat_days[k].id) {
                          var subItem = angular.copy(item);
                          subItem.start = new Date(currentDay);
                          subItem.start.setHours(item.schedule_repeat_from ? item.schedule_repeat_from : 0);
                          subItem.start.setMinutes(0);
                          subItem.start.setSeconds(0);
                          subItem.start = subItem.start.getTime();
                          subItem.end = new Date(currentDay);
                          subItem.end.setHours(item.schedule_repeat_to ? item.schedule_repeat_to : 23);
                          subItem.end.setMinutes(item.schedule_repeat_to ? 0 : 59);
                          subItem.end.setSeconds(item.schedule_repeat_to ? 0 : 59);
                          subItem.end = subItem.end.getTime();

                          // Create unique id for the subItem.
                          subItem.id = item.id + "_" + j;

                          // @TODO: Handle subItem.end subItem.start overflowing item.start and item.end, should be bound the interval.

                          items.push(subItem);
                        }
                      }

                      currentDay.setDate(currentDay.getDate() + 1);
                      j++;
                    }
                  }
                }
              }
            }

            timeline.setItems(items);
          };

          // Configuration for the Timeline
          var options = {
            locale: 'da',                               // set language to danish, requires moment-with-locales.min.js
            editable: false,                            // disable editing
            snap: function (date) {                     // snap to hour
              var hour = 60 * 60 * 1000;
              return Math.round(date / hour) * hour;
            },
            min: new Date(2016, 0, 1),                  // minimum date shown in timeline: January 1. 2016
            zoomMin: 1000 * 60 * 60 * 24,               // one day in milliseconds
            zoomMax: 1000 * 60 * 60 * 24 * 31,          // about one month in milliseconds
            stack: false,                               // disable stacking to allow subgroups to share a line in the timeline
            zoomable: true                              // remove zoomable
          };

          // Listen for when scope is ready.
          scope.$watch('data', function (oldVal, newVal) {
            if (newVal !== null) {
              // DOM element where the Timeline will be attached
              var container = document.getElementById("timeline_" + scope.data.id);

              // Create a Timeline
              timeline = new vis.Timeline(container, [], scope.data.groups, options);

              // Register listener for 'rangechanged'.
              //   This should trigger a data reload.
              timeline.on('rangechanged', function (properties) {
                // Update window and recalculate data.
                //   Timeout to avoid digest errors, since timeline events are outside angular.
                $timeout(function() {
                  scope.start = new Date(properties.start);
                  scope.end = new Date(properties.end);

                  calculateData();
                });
              });

              // Register double click listener
              //   Redirects to item.redirect_url
              timeline.on('doubleClick', function (properties) {
                // Find item.
                for (var item in items) {
                  item = items[item];
                  if (item.id == properties.item) {
                    scope.$apply(function() {
                      $location.path(item.redirect_url);
                    });
                    return;
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

          /**
           * Move window to today. Loads week.
           */
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
