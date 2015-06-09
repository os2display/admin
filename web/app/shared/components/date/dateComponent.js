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
  app.directive('dateComponent', ['$interval', 'configuration',
    function ($interval, configuration) {
      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'app/shared/components/date/date.html?' + configuration.version,
        scope: {
          theme: '@'
        },
        link: function (scope) {
          scope.thisDate = new Date();

          // Update current date every minute.
          var interval = $interval(function() {
            // Update current datetime.
            scope.thisDate = new Date();
          }, 60000);

          // Register event listener for destroy.
          //   Cleanup interval.
          scope.$on('$destroy', function() {
            if (angular.isDefined(interval)) {
              $interval.cancel(interval);
              interval = undefined;
            }
          });
        }
      };
    }
  ]);
}).call(this);
