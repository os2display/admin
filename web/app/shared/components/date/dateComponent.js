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
  app = angular.module('itkDateComponent', []);

  /**
   * date component directive.
   *
   * html parameters:
   */
  app.directive('dateComponent', ['$interval',
    function ($interval) {
      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'app/shared/components/date/date.html',
        scope: {
        },
        link: function (scope) {
          scope.thisDate = new Date();

          // Update current date every minute.
          $interval(function() {
            // Update current datetime.
            scope.thisDate = new Date();
          }, 60000);
        }
      };
    }
  ]);
}).call(this);
