/**
 * @file
 * Contains the itkDateComponent module.
 */

/**
 * Setup the module.
 */
(function () {
  'use strict';

  var app;
  app = angular.module('itkDigitalClockComponent', []);

  /**
   * date component directive.
   *
   * html parameters:
   */
  app.directive('digitalClockComponent', ['$interval',
    function ($interval) {
      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'app/shared/components/digital-clock/digital-clock.html',
        scope: {
        },
        link: function (scope) {
          scope.thisDate = new Date();

          // Update current date every minute.
          $interval(function() {
            // Update current datetime.
            scope.thisDate = Date.now();
          }, 1000);
        }
      };
    }
  ]);
}).call(this);
