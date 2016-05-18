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
          id: '@'
        },
        link: function (scope) {
          // DOM element where the Timeline will be attached
          var container = document.getElementById(scope.id);

          // Create a DataSet (allows two way data-binding)
          var items = new vis.DataSet([
            {
              id: 1,
              content: 'item 1',
              start: 1463472027000,
              end: 1463492227000,
              editable: false
            },
            {
              id: 2,
              content: 'item 2',
              start: 1463472027000,
              end: 1463592227000
            },
            {
              id: 3,
              content: 'item 3',
              start: 1463472227000,
              end: 1463792227000
            },
            {
              id: 4,
              content: 'item 4',
              start: 1463782227000,
              end: 1463792227000
            },
            {
              id: 5,
              content: 'item 5',
              start: 1463882227000,
              end: 1463892227000
            },
            {id: 6, content: 'item 6', start: 1463982227000, end: 1463992227000}
          ]);

          // Configuration for the Timeline
          var options = {
            locale: 'da',                              // set language to danish, requires moment-with-locales.min.js
            editable: {                                // make items editable, only update time
              add: false,
              updateTime: true,
              updateGroup: false,
              remove: false
            },
            snap: function (date, scale, step) {       // snap to hour
              var hour = 60 * 60 * 1000;
              return Math.round(date / hour) * hour;
            },
            zoomMin: 1000 * 60 * 60 * 24,              // one day in milliseconds
            zoomMax: 1000 * 60 * 60 * 24 * 31 * 3      // about 3 months in milliseconds
          };

          // Create a Timeline
          var timeline = new vis.Timeline(container, items, options);

          // Set starting window
          timeline.setWindow(1463474027000, 1463474027000 + 1000 * 24 * 60 * 60 * 7);

          scope.move = function move(percentage)
          {
            var range = timeline.getWindow();
            var interval = range.end - range.start;

            timeline.setWindow({
              start: range.start.valueOf() - interval * percentage,
              end: range.end.valueOf() - interval * percentage
            });
          };

          scope.zoom = function zoom(percentage) {
            var range = timeline.getWindow();
            var interval = range.end - range.start;

            timeline.setWindow({
              start: range.start.valueOf() - interval * percentage,
              end: range.end.valueOf() + interval * percentage
            });
          };
        }
      };
    }
  ]);