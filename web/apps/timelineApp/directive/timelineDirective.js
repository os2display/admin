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
            zoomable: false,
            zoomMin: 1000 * 60 * 60 * 24,              // one day in milliseconds
            zoomMax: 1000 * 60 * 60 * 24 * 31 * 3      // about 3 months in milliseconds
          };

          scope.$watch('id', function(oldVal, newVal) {
            if (newVal !== null) {

              console.log(scope.data);
              
              // DOM element where the Timeline will be attached
              var container = document.getElementById(scope.id);

              // Create a DataSet (allows two way data-binding)
              var items = new vis.DataSet(scope.data.channels);

              // Create a Timeline
              timeline = new vis.Timeline(container, items, scope.data.regions, options);

              // Set starting window
              timeline.setWindow(1463474027000, 1463474027000 + 1000 * 24 * 60 * 60 * 7);
            }
          });

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