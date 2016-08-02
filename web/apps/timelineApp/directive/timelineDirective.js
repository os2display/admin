/**
 * Timeline directive.
 *
 * Built from http://visjs.org/
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
          var items;
          var groups;
          var focusDate;

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
           * Calculates calendar data for the current range between scope.start and scope.end
           *
           * @TODO: Move out of directive to service.
           */
          function calculateData() {
            var items = [];

            // Iterate all items, and evaluate if/how they should be added to the timeline.
            for (var i = 0; i < scope.data.items.length; i++) {
              var item = angular.copy(scope.data.items[i]);

              // If item is to be shown within the current range.
              if (
                (!item.start || item.start < scope.end.getTime()) &&
                (!item.end || item.end > scope.start.getTime())) {

                // If item.start has not been set, set it to the start of the current range (representing infinite)
                if (!item.start) {
                  item.start = scope.start.getTime();
                }

                // If item.end has not been set, set it to the end of the current range (representing infinite)
                if (!item.end) {
                  item.end = scope.end.getTime();
                }

                // If the item does not have a schedule_repeat field or it is false
                if (!item.schedule_repeat) {
                  items.push(item);
                }
                else {
                  // Only add to the scheduled days, in the given interval.
                  if (item.schedule_repeat_days) {
                    var currentDay = new Date(scope.start);

                    var j = 0;

                    while (currentDay < scope.end) {
                      for (var k = 0; k < item.schedule_repeat_days.length; k++) {
                        if (currentDay.getDay() === item.schedule_repeat_days[k].id) {
                          var subItem = angular.copy(item);

                          // Set subItem.start
                          subItem.start = new Date(currentDay);
                          subItem.start.setHours(item.schedule_repeat_from ? item.schedule_repeat_from : 0);
                          subItem.start.setMinutes(0);
                          subItem.start.setSeconds(0);
                          subItem.start = subItem.start.getTime();

                          // Set subItem.end
                          subItem.end = new Date(currentDay);
                          subItem.end.setHours(item.schedule_repeat_to ? item.schedule_repeat_to : 23);
                          subItem.end.setMinutes(item.schedule_repeat_to ? 0 : 59);
                          subItem.end.setSeconds(item.schedule_repeat_to ? 0 : 59);
                          subItem.end = subItem.end.getTime();

                          // Make sure we have not overlapped the item.start by the subItem
                          if (subItem.end < item.start) {
                            continue;
                          }

                          // Make sure we have not overlapped the item.end by the subItem
                          if (subItem.start > item.end) {
                            continue;
                          }

                          if (subItem.start < item.start) {
                            subItem.start = item.start;
                          }
                          // Make sure the subItem.end does not overlap item.end
                          if (subItem.end > item.end) {
                            subItem.end = item.end;
                          }

                          // Create unique id for the subItem.
                          subItem.id = item.id + "_" + j;

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

            return items;
          }

          /**
           * Calculate the groups to show for a given array of items.
           *
           * @param items
           * @returns {Array}
           */
          function calculateGroups(items) {
            var groupIds = [];

            // Gather group ids in items
            for (var item in items) {
              item = items[item];

              if (groupIds.indexOf(item.group) == -1) {
                groupIds.push(item.group);
              }
            }

            var groups = [];

            // Add groups that are attached to an item.
            for (var group in scope.data.groups) {
              group = scope.data.groups[group];

              if (groupIds.indexOf(group.id) !== -1) {
                groups.push(group);
              }
            }

            return groups;
          }

          /**
           * Update the data for the timeline.
           *
           * @param properties
           */
          function updateTimeline(properties) {
            // Update window and recalculate data.
            //   Timeout to avoid digest errors, since timeline events are outside angular.
            $timeout(function () {
              scope.start = new Date(properties.start);
              scope.end = new Date(properties.end);

              items = calculateData();
              groups = calculateGroups(items);

              timeline.setData({
                'items': items,
                'groups': groups
              });
            });
          }

          // Configuration for the Timeline
          var options = {
            locales: {
              da: {
                current: 'nuværende',
                time: 'tid'
              }
            },
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
            zoomable: false                             // remove zoomable
          };

          // Listen for when scope is ready.
          scope.$watch('data', function (oldVal, newVal) {
            if (newVal !== null) {
              // DOM element where the Timeline will be attached
              var container = document.getElementById("timeline_" + scope.data.id);

              // Create a Timeline
              timeline = new vis.Timeline(container, [], [], options);

              // Initialize window to week window surrounding today.
              scope.today();

              // Set scope.start and scope.end
              var window = timeline.getWindow();
              scope.start = new Date(window.start);
              scope.end = new Date(window.end);

              // Load initial timeline data.
              items = calculateData();
              groups = calculateGroups(items);

              // Register listener for 'rangechanged'.
              //   This triggers a data reload.
              timeline.on('rangechanged', updateTimeline);

              // Register double click listener
              //   Redirects to item.redirect_url
              timeline.on('click', function (properties) {
                // Find item.
                for (var item in items) {
                  item = items[item];
                  if (item.id == properties.item) {
                    scope.$apply(function () {
                      $location.path(item.redirect_url);
                    });
                    return;
                  }
                }
              });

              timeline.setData({
                items: items,
                groups: groups
              });
            }
          });

          /**
           * Move window @days number of days.
           * @param days
           */
          scope.moveDays = function moveDays(days) {
            var displacement = days * 24 * 60 * 60 * 1000;

            focusDate = new Date(focusDate.getTime() + displacement);
            calculateWeekWindow(focusDate);
          };

          /**
           * Move window to today. Loads week.
           */
          scope.today = function today() {
            focusDate = new Date();
            calculateWeekWindow(focusDate);
          };

          /**
           * Zoom the timeline a given percentage in or out
           * @param {Number} percentage   For example 0.1 (zoom out) or -0.1 (zoom in)
           */
          scope.zoom = function (percentage) {
            var range = timeline.getWindow();
            var interval = range.end - range.start;

            timeline.setWindow({
              start: range.start.valueOf() - interval * percentage,
              end: range.end.valueOf() + interval * percentage,
              animation: false
            });
          };

          // Register event listener for destroy.
          scope.$on('$destroy', function () {
            if (timeline) {
              timeline.off();
            }
          });
        }
      };
    }
  ]);
